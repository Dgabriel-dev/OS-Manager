import api from './client';
import type { ApiResponse, StockItem, StockMovement, StockCategory, PaginatedResponse } from '@/types';

export interface StockItemFilters {
  search?: string;
  category_id?: number;
  is_low_stock?: boolean;
  page?: number;
  per_page?: number;
}

export interface StockMovementFilters {
  item_id?: number;
  type?: string;
  page?: number;
  per_page?: number;
}

export const stockApi = {
  listItems: async (filters?: StockItemFilters) => {
    const response = await api.get<PaginatedResponse<StockItem>>('/stock/items', { params: filters });
    return response.data;
  },

  getItemById: async (id: number) => {
    const response = await api.get<ApiResponse<StockItem>>(`/stock/items/${id}`);
    return response.data.data;
  },

  createItem: async (data: Partial<StockItem>) => {
    const response = await api.post<ApiResponse<StockItem>>('/stock/items', data);
    return response.data.data;
  },

  updateItem: async (id: number, data: Partial<StockItem>) => {
    const response = await api.put<ApiResponse<StockItem>>(`/stock/items/${id}`, data);
    return response.data.data;
  },

  deleteItem: async (id: number) => {
    const response = await api.delete(`/stock/items/${id}`);
    return response.data;
  },

  adjustStock: async (id: number, data: { type: string; quantity: number; notes?: string }) => {
    const response = await api.put<ApiResponse<StockItem>>(`/stock/items/${id}/adjust`, data);
    return response.data.data;
  },

  listCategories: async () => {
    const response = await api.get<ApiResponse<StockCategory[]>>('/stock/categories');
    return response.data.data;
  },

  listMovements: async (filters?: StockMovementFilters) => {
    const response = await api.get<PaginatedResponse<StockMovement>>('/stock/movements', { params: filters });
    return response.data;
  },
};
