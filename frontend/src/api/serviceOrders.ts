import api from './client';
import type { ApiResponse, ServiceOrder, OrderHistory, PaginatedResponse } from '@/types';

export interface ServiceOrderFilters {
  search?: string;
  status?: string;
  priority?: string;
  technician_id?: number;
  client_id?: number;
  page?: number;
  per_page?: number;
}

export const serviceOrdersApi = {
  list: async (filters?: ServiceOrderFilters) => {
    const response = await api.get<PaginatedResponse<ServiceOrder>>('/service-orders', { params: filters });
    return response.data;
  },

  getById: async (id: number) => {
    const response = await api.get<ApiResponse<ServiceOrder>>(`/service-orders/${id}`);
    return response.data.data;
  },

  create: async (data: Partial<ServiceOrder>) => {
    const response = await api.post<ApiResponse<ServiceOrder>>('/service-orders', data);
    return response.data.data;
  },

  update: async (id: number, data: Partial<ServiceOrder>) => {
    const response = await api.put<ApiResponse<ServiceOrder>>(`/service-orders/${id}`, data);
    return response.data.data;
  },

  delete: async (id: number) => {
    const response = await api.delete(`/service-orders/${id}`);
    return response.data;
  },

  restore: async (id: number) => {
    const response = await api.post(`/service-orders/${id}/restore`);
    return response.data;
  },

  updateStatus: async (id: number, status: string) => {
    const response = await api.put<ApiResponse<ServiceOrder>>(`/service-orders/${id}/status`, { status });
    return response.data.data;
  },

  getHistory: async (id: number) => {
    const response = await api.get<ApiResponse<OrderHistory[]>>(`/service-orders/${id}/history`);
    return response.data.data;
  },

  addItem: async (id: number, item: { description: string; quantity: number; unit_price: number; type: string }) => {
    const response = await api.post<ApiResponse<any>>(`/service-orders/${id}/items`, item);
    return response.data.data;
  },

  removeItem: async (orderId: number, itemId: number) => {
    const response = await api.delete(`/service-orders/${orderId}/items/${itemId}`);
    return response.data;
  },
};
