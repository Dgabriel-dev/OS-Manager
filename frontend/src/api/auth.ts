import api from './client';
import type { ApiResponse, User } from '@/types';

export interface LoginRequest {
  email: string;
  password: string;
}

export interface LoginResponse {
  user: User;
  token: string;
}

export const authApi = {
  login: async (data: LoginRequest) => {
    const response = await api.post<LoginResponse>('/login', data);
    return response.data;
  },

  logout: async () => {
    const response = await api.post('/logout');
    return response.data;
  },

  me: async () => {
    const response = await api.get<{ user: User }>('/me');
    return response.data.user;
  },

  updateProfile: async (data: Partial<User>) => {
    const response = await api.put<{ user: User; message: string }>('/profile', data);
    return response.data;
  },

  changePassword: async (data: { current_password: string; password: string; password_confirmation: string }) => {
    const response = await api.put<{ message: string }>('/password', data);
    return response.data;
  },
};
