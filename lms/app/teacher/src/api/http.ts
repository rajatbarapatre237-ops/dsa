import axios, { AxiosError, AxiosInstance } from 'axios';
import { logoutSession } from '../auth/authSession';
import { API_BASE_URL, APP_ROLE } from '../config';
import { AppStorage } from '../storage/AppStorage';

export type ApiError = {
  status: number | null;
  message: string;
};

export function createHttpClient(): AxiosInstance {
  const instance = axios.create({
    baseURL: API_BASE_URL,
    timeout: 20000,
    headers: { 'Content-Type': 'application/json' },
  });

  instance.interceptors.request.use(async config => {
    const token = await AppStorage.getToken();
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    if (config.data instanceof FormData) {
      if (typeof config.headers.delete === 'function') {
        config.headers.delete('Content-Type');
      } else {
        delete config.headers['Content-Type'];
      }
    }
    return config;
  });

  instance.interceptors.response.use(
    r => r,
    (error: AxiosError<{ message?: string }>) => {
      const status = error.response?.status ?? null;
      const requestUrl = error.config?.url ?? '';
      const isLoginRequest = new RegExp(`/auth/${APP_ROLE}/login`).test(requestUrl);

      if (status === 401 && !isLoginRequest) {
        void logoutSession();
      }

      let message = error.response?.data?.message;
      const validationErrors = error.response?.data?.errors as Record<string, string[]> | undefined;
      if (validationErrors) {
        const first = Object.values(validationErrors).flat()[0];
        if (first) message = first;
      }

      if (!message) {
        const code = error.code ?? '';
        const isNetworkFailure =
          !error.response &&
          (code === 'ERR_NETWORK' ||
            code === 'ECONNABORTED' ||
            /network error/i.test(error.message ?? ''));

        message = isNetworkFailure
          ? `Cannot reach API at ${API_BASE_URL}. Start the backend: ./start-backend.sh (and ensure MySQL is running).`
          : status === 500
            ? 'Server error — deploy the latest backend and run the database upgrade for multi-file uploads.'
            : error.message || 'Request failed';
      }

      return Promise.reject({
        status: error.response?.status ?? null,
        message,
      } as ApiError);
    },
  );

  return instance;
}

export const http = createHttpClient();
