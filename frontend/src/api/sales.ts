import api from './client';
import type { ApiResponse, Sale, SaleCategory, PaginatedResponse } from '@/types';

export interface SaleFilters {
  search?: string;
  payment_status?: string;
  page?: number;
  per_page?: number;
}

export interface MonthlyRevenue {
  month: string;
  label: string;
  income: number;
  transaction_income: number;
  sale_revenue: number;
  expense: number;
  balance: number;
  cumulative: number;
}

export interface RevenueByMonthResponse {
  months: MonthlyRevenue[];
  cumulative_revenue: number;
  current_month_revenue: number;
}

export const salesApi = {
  list: async (filters?: SaleFilters) => {
    const response = await api.get<PaginatedResponse<Sale>>('/sales', { params: filters });
    return response.data;
  },

  getById: async (id: number) => {
    const response = await api.get<ApiResponse<Sale>>(`/sales/${id}`);
    return response.data.data;
  },

  create: async (data: { client_id?: number | null; payment_method?: string | null; payment_status?: string; notes?: string | null; items: any[] }) => {
    const response = await api.post<ApiResponse<Sale>>('/sales', data);
    return response.data.data;
  },

  update: async (id: number, data: { client_id?: number | null; payment_method?: string | null; payment_status?: string; notes?: string | null; items?: any[] }) => {
    const response = await api.put<ApiResponse<Sale>>(`/sales/${id}`, data);
    return response.data.data;
  },

  delete: async (id: number) => {
    const response = await api.delete(`/sales/${id}`);
    return response.data;
  },

  listCategories: async () => {
    const response = await api.get<ApiResponse<SaleCategory[]>>('/sales/categories');
    return response.data.data;
  },

  createCategory: async (data: { name: string }) => {
    const response = await api.post<ApiResponse<SaleCategory>>('/sales/categories', data);
    return response.data.data;
  },
};
