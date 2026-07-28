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
  total_expenses: number;
  balance: number;
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
};
