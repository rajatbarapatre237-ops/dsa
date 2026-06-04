import { APP_ROLE } from '../config';
import { http } from './http';

export type LoginResponse = {
  status: string;
  token: string;
  user: Record<string, unknown>;
};

export const AuthApi = {
  async login(payload: Record<string, string>): Promise<LoginResponse> {
    const { data } = await http.post<LoginResponse>(
      `/auth/${APP_ROLE}/login`,
      payload,
    );
    return data;
  },
  async logout(): Promise<void> {
    try {
      await http.post('/auth/logout');
    } catch {
      /* ignore */
    }
  },
};

export const DashboardApi = {
  async getDashboard(): Promise<unknown> {
    const { data } = await http.get(`/${APP_ROLE}/dashboard`);
    return data;
  },
};
