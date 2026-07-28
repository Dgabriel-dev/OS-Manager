import api from './client';
import type { ApiResponse, Equipment, PaginatedResponse } from '@/types';

export interface EquipmentFilters {
  search?: string;
  category?: string;
  client_id?: number;
  page?: number;
  per_page?: number;
}

export const equipmentApi = {
  list: async (filters?: EquipmentFilters) => {
    const response = await api.get<PaginatedResponse<Equipment>>('/equipment', { params: filters });
    return response.data;
  },

  getById: async (id: number) => {
    const response = await api.get<ApiResponse<Equipment>>(`/equipment/${id}`);
    return response.data.data;
  },

  create: async (data: Partial<Equipment>) => {
    const response = await api.post<ApiResponse<Equipment>>('/equipment', data);
    return response.data.data;
  },

  update: async (id: number, data: Partial<Equipment>) => {
    const response = await api.put<ApiResponse<Equipment>>(`/equipment/${id}`, data);
    return response.data.data;
  },

  delete: async (id: number) => {
    const response = await api.delete(`/equipment/${id}`);
    return response.data;
  },

  restore: async (id: number) => {
    const response = await api.post(`/equipment/${id}/restore`);
    return response.data;
  },

  byClient: async (clientId: number, perPage?: number) => {
    const response = await api.get<PaginatedResponse<Equipment>>(`/clients/${clientId}/equipment`, {
      params: { per_page: perPage },
    });
    return response.data;
  },

  uploadFile: async (id: number, file: File) => {
    const formData = new FormData();
    formData.append('file', file);
    const response = await api.post<ApiResponse<any>>(`/equipment/${id}/files`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return response.data.data;
  },

  deleteFile: async (equipmentId: number, fileId: number) => {
    const response = await api.delete(`/equipment/${equipmentId}/files/${fileId}`);
    return response.data;
  },
};
