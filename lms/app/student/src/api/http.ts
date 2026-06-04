import axios, { AxiosError, AxiosInstance } from 'axios';
import { API_BASE_URL } from '../config';
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
    return config;
  });

  instance.interceptors.response.use(
    r => r,
    (error: AxiosError<{ message?: string }>) => {
      const message =
        error.response?.data?.message ||
        error.message ||
        'Network error';
      return Promise.reject({
        status: error.response?.status ?? null,
        message,
      } as ApiError);
    },
  );

  return instance;
}

export const http = createHttpClient();
