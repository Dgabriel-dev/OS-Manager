import api from './client';

interface DashboardResponse {
  stats: {
    total_clients: number;
    total_equipment: number;
    orders_by_status: Record<string, number>;
    monthly_revenue: number;
    low_stock_count: number;
  };
  monthly_orders: any[];
  revenue_chart: any[];
  top_equipments: any[];
  recent_orders: any[];
}

export const dashboardApi = {
  getStats: async () => {
    const response = await api.get<{ data: DashboardResponse }>('/dashboard');
    return response.data.data;
  },
};
