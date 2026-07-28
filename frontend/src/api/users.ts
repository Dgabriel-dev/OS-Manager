import api from './client';
import type { ApiResponse, User, Role, PaginatedResponse } from '@/types';

export interface UserFilters {
  search?: string;
  role_id?: number;
  is_active?: boolean;
  page?: number;
  per_page?: number;
}

export const usersApi = {
  list: async (filters?: UserFilters) => {
    const response = await api.get<PaginatedResponse<User>>('/users', { params: filters });
    return response.data;
  },

  getById: async (id: number) => {
    const response = await api.get<ApiResponse<User>>(`/users/${id}`);
    return response.data.data;
  },

  create: async (data: Partial<User> & { password?: string; role_id?: number }) => {
    const response = await api.post<ApiResponse<User>>('/users', data);
    return response.data.data;
  },

  update: async (id: number, data: Partial<User> & { password?: string; role_id?: number }) => {
    const response = await api.put<ApiResponse<User>>(`/users/${id}`, data);
    return response.data.data;
  },

  delete: async (id: number) => {
    const response = await api.delete(`/users/${id}`);
    return response.data;
  },

  listRoles: async () => {
    const response = await api.get<ApiResponse<Role[]>>('/roles');
    return response.data.data;
  },
};
