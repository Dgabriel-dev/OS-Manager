import api from './client';
import type { ApiResponse, Transaction, FinancialCategory, PaginatedResponse } from '@/types';

export interface TransactionFilters {
  search?: string;
  type?: string;
  status?: string;
  category_id?: number;
  start_date?: string;
  end_date?: string;
  page?: number;
  per_page?: number;
}

export interface FinancialSummary {
  total_income: number;
  total_expense: number;
  balance: number;
  monthly_income: number;
  monthly_expense: number;
  monthly_balance: number;
  total_orders: number;
  orders_by_status: Record<string, number>;
  pending_revenue: number;
  completed_revenue: number;
  monthly_completed_revenue: number;
  monthly_orders_created: number;
  total_sale_revenue: number;
  total_sale_cost: number;
  total_sale_profit: number;
  monthly_sale_revenue: number;
  monthly_sale_cost: number;
  monthly_sale_profit: number;
}

export interface MonthlyRevenue {
  month: string;
  label: string;
  income: number;
  transaction_income: number;
  sale_revenue: number;
  sale_cost: number;
  sale_profit: number;
  expense: number;
  balance: number;
  cumulative: number;
}

export interface RevenueByMonthResponse {
  months: MonthlyRevenue[];
  cumulative_revenue: number;
  current_month_revenue: number;
}

export const financialApi = {
  listTransactions: async (filters?: TransactionFilters) => {
    const response = await api.get<PaginatedResponse<Transaction>>('/financial/transactions', { params: filters });
    return response.data;
  },

  getTransactionById: async (id: number) => {
    const response = await api.get<ApiResponse<Transaction>>(`/financial/transactions/${id}`);
    return response.data.data;
  },

  createTransaction: async (data: Partial<Transaction>) => {
    const response = await api.post<ApiResponse<Transaction>>('/financial/transactions', data);
    return response.data.data;
  },

  updateTransaction: async (id: number, data: Partial<Transaction>) => {
    const response = await api.put<ApiResponse<Transaction>>(`/financial/transactions/${id}`, data);
    return response.data.data;
  },

  deleteTransaction: async (id: number) => {
    const response = await api.delete(`/financial/transactions/${id}`);
    return response.data;
  },

  getDashboard: async () => {
    const response = await api.get<ApiResponse<FinancialSummary>>('/financial/dashboard');
    return response.data.data;
  },

  getRevenueByMonth: async () => {
    const response = await api.get<ApiResponse<RevenueByMonthResponse>>('/financial/revenue-by-month');
    return response.data.data;
  },

  listCategories: async () => {
    const response = await api.get<ApiResponse<FinancialCategory[]>>('/financial/categories');
    return response.data.data;
  },
};
