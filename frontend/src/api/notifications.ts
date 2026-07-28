import api from './client';
import type { ApiResponse, Notification, PaginatedResponse } from '@/types';

export const notificationsApi = {
  list: async (page?: number) => {
    const response = await api.get<PaginatedResponse<Notification>>('/notifications', { params: { page } });
    return response.data;
  },

  markAsRead: async (id: number) => {
    const response = await api.put<ApiResponse<Notification>>(`/notifications/${id}/read`);
    return response.data.data;
  },

  markAllAsRead: async () => {
    const response = await api.put('/notifications/read-all');
    return response.data;
  },

  getUnreadCount: async () => {
    const response = await api.get<ApiResponse<{ count: number }>>('/notifications/unread-count');
    return response.data.data;
  },
};
