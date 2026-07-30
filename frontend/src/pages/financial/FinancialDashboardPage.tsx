import { useQuery } from '@tanstack/react-query';
import { DollarSign, TrendingUp, TrendingDown, Clock, Wrench, Package, CheckCircle, AlertCircle } from 'lucide-react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from 'recharts';
import { financialApi } from '@/api/financial';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { StatCard } from '@/components/ui/stat-card';
import { LoadingPage } from '@/components/ui/loading-spinner';
import { formatCurrency } from '@/utils/formatters';

const statusLabels: Record<string, string> = {
  open: 'Abertas',
  in_progress: 'Em Andamento',
  waiting_parts: 'Aguardando Peças',
  completed: 'Concluídas',
  delivered: 'Entregues',
  cancelled: 'Canceladas',
};

export function FinancialDashboardPage() {
  const { data: summary, isLoading: loadingSummary } = useQuery({
    queryKey: ['financial-summary'],
    queryFn: financialApi.getDashboard,
  });

  const { data: revenueData, isLoading: loadingRevenue } = useQuery({
    queryKey: ['financial-revenue-by-month'],
    queryFn: financialApi.getRevenueByMonth,
  });

  if (loadingSummary || loadingRevenue) return <LoadingPage />;

  const chartData = revenueData?.months?.map(m => ({
    name: m.label,
    Receita: m.income,
    Despesa: m.expense,
    Lucro: m.sale_profit || 0,
  })) || [];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Financeiro</h1>
        <p className="text-muted-foreground">Visão geral financeira e de ordens de serviço</p>
      </div>

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <StatCard
          icon={<TrendingUp className="h-5 w-5" />}
          label="Receita Total"
          value={formatCurrency((summary?.total_income || 0) + (summary?.total_sale_revenue || 0))}
        />
        <StatCard
          icon={<TrendingDown className="h-5 w-5" />}
          label="Despesas Totais"
          value={formatCurrency((summary?.total_expense || 0) + (summary?.total_sale_cost || 0))}
        />
        <StatCard
          icon={<DollarSign className="h-5 w-5" />}
          label="Lucro Total"
          value={formatCurrency((summary?.total_income || 0) - (summary?.total_expense || 0) + (summary?.total_sale_profit || 0))}
        />
        <StatCard
          icon={<Clock className="h-5 w-5" />}
          label="Saldo Mensal"
          value={formatCurrency((summary?.monthly_balance || 0) + (summary?.monthly_sale_profit || 0))}
        />
      </div>

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <StatCard
          icon={<Wrench className="h-5 w-5" />}
          label="Total de OS"
          value={String(summary?.total_orders || 0)}
        />
        <StatCard
          icon={<Package className="h-5 w-5" />}
          label="Receita Pendente (OS)"
          value={formatCurrency(summary?.pending_revenue || 0)}
        />
        <StatCard
          icon={<CheckCircle className="h-5 w-5" />}
          label="Receita Realizada (OS)"
          value={formatCurrency(summary?.completed_revenue || 0)}
        />
        <StatCard
          icon={<AlertCircle className="h-5 w-5" />}
          label="OS Criadas no Mês"
          value={String(summary?.monthly_orders_created || 0)}
        />
      </div>

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Lucro Total (Vendas)</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-bold text-blue-600">
              {formatCurrency(summary?.total_sale_profit || 0)}
            </p>
            <p className="text-xs text-muted-foreground mt-1">
              Receita: {formatCurrency(summary?.total_sale_revenue || 0)}
            </p>
          </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Custo Total (Vendas)</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-bold text-red-600">
              {formatCurrency(summary?.total_sale_cost || 0)}
            </p>
            <p className="text-xs text-muted-foreground mt-1">
              Lucro: {formatCurrency(summary?.total_sale_profit || 0)}
            </p>
          </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Mês Atual (Vendas)</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-bold text-green-600">
              {formatCurrency(summary?.monthly_sale_revenue || 0)}
            </p>
            <p className="text-xs text-muted-foreground mt-1">
              Custo: {formatCurrency(summary?.monthly_sale_cost || 0)} | Lucro: {formatCurrency(summary?.monthly_sale_profit || 0)}
            </p>
          </CardContent>
        </Card>
      </div>

      {chartData.length > 0 && (
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Receita vs Despesa (Últimos 12 Meses)</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="h-[400px]">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={chartData} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" fontSize={12} tickLine={false} />
                  <YAxis fontSize={12} tickLine={false} tickFormatter={(value) => `R$ ${(value / 1000).toFixed(0)}k`} />
                  <Tooltip formatter={(value) => formatCurrency(Number(value))} />
                  <Legend />
                  <Bar dataKey="Receita" fill="#22c55e" radius={[4, 4, 0, 0]} />
                  <Bar dataKey="Despesa" fill="#ef4444" radius={[4, 4, 0, 0]} />
                  <Bar dataKey="Lucro" fill="#3b82f6" radius={[4, 4, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            </div>
          </CardContent>
        </Card>
      )}

      <div className="grid gap-4 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Receitas Mensais (Transações)</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-bold text-green-600">
              {formatCurrency(summary?.monthly_income || 0)}
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Despesas Mensais</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-bold text-red-600">
              {formatCurrency(summary?.monthly_expense || 0)}
            </p>
          </CardContent>
        </Card>
      </div>

      <div className="grid gap-4 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Receita Concluída no Mês (OS)</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-bold text-emerald-600">
              {formatCurrency(summary?.monthly_completed_revenue || 0)}
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">OS por Status</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-2">
              {summary?.orders_by_status && Object.entries(summary.orders_by_status).length > 0 ? (
                Object.entries(summary.orders_by_status).map(([status, count]) => (
                  <div key={status} className="flex justify-between text-sm">
                    <span className="text-muted-foreground">{statusLabels[status] || status}</span>
                    <span className="font-medium">{count}</span>
                  </div>
                ))
              ) : (
                <p className="text-sm text-muted-foreground">Nenhuma OS registrada</p>
              )}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
