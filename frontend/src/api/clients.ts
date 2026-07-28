import api from './client';
import type { ApiResponse, Client, PaginatedResponse } from '@/types';

export interface ClientFilters {
  search?: string;
  page?: number;
  per_page?: number;
}

export const clientsApi = {
  list: async (filters?: ClientFilters) => {
    const response = await api.get<PaginatedResponse<Client>>('/clients', { params: filters });
    return response.data;
  },

  getById: async (id: number) => {
    const response = await api.get<ApiResponse<Client>>(`/clients/${id}`);
    return response.data.data;
  },

  create: async (data: Partial<Client>) => {
    const response = await api.post<ApiResponse<Client>>('/clients', data);
    return response.data.data;
  },

  update: async (id: number, data: Partial<Client>) => {
    const response = await api.put<ApiResponse<Client>>(`/clients/${id}`, data);
    return response.data.data;
  },

  delete: async (id: number) => {
    const response = await api.delete(`/clients/${id}`);
    return response.data;
  },

  restore: async (id: number) => {
    const response = await api.post(`/clients/${id}/restore`);
    return response.data;
  },
};
