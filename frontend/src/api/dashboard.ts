import api from './client';
import type { ApiResponse, DashboardStats } from '@/types';

export const dashboardApi = {
  getStats: async () => {
    const response = await api.get<ApiResponse<DashboardStats>>('/dashboard');
    return response.data.data;
  },
};
