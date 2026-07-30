import { useQuery } from '@tanstack/react-query';
import { Users, Monitor, FileText, DollarSign, AlertTriangle, Package } from 'lucide-react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from 'recharts';
import { dashboardApi } from '@/api/dashboard';
import { StatCard } from '@/components/ui/stat-card';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { LoadingPage } from '@/components/ui/loading-spinner';
import { formatCurrency, formatDate } from '@/utils/formatters';

const statusLabels: Record<string, { label: string; variant: 'default' | 'success' | 'warning' | 'info' | 'destructive' }> = {
  open: { label: 'Aberta', variant: 'info' },
  in_progress: { label: 'Em Andamento', variant: 'warning' },
  waiting_parts: { label: 'Aguardando Peças', variant: 'warning' },
  completed: { label: 'Concluída', variant: 'success' },
  delivered: { label: 'Entregue', variant: 'success' },
  cancelled: { label: 'Cancelada', variant: 'destructive' },
};

export function DashboardPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['dashboard-stats'],
    queryFn: dashboardApi.getStats,
  });

  if (isLoading) return <LoadingPage />;

  const stats = data?.stats;
  const recentOrders = data?.recent_orders || [];
  const lowStockItems = data?.low_stock_items || [];
  const revenueChart = data?.revenue_chart || [];
  const monthlyOrders = data?.monthly_orders || [];

  const revenueData = revenueChart.map((item: any) => ({
    name: item.month,
    Receita: item.income,
    Despesa: item.expense,
  }));

  const ordersData = monthlyOrders.map((item: any) => ({
    name: item.month,
    OS: item.count,
  }));

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Dashboard</h1>
        <p className="text-muted-foreground">Visão geral do sistema</p>
      </div>

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <StatCard
          icon={<Users className="h-5 w-5" />}
          label="Total de Clientes"
          value={stats?.total_clients || 0}
        />
        <StatCard
          icon={<Monitor className="h-5 w-5" />}
          label="Equipamentos"
          value={stats?.total_equipment || 0}
        />
        <StatCard
          icon={<FileText className="h-5 w-5" />}
          label="OS Abertas"
          value={stats?.orders_by_status?.open || 0}
        />
        <StatCard
          icon={<DollarSign className="h-5 w-5" />}
          label="Receita Mensal"
          value={formatCurrency(stats?.monthly_revenue || 0)}
        />
      </div>

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader>
            <CardTitle className="text-base">OS em Andamento</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-3xl font-bold text-blue-600">{stats?.orders_by_status?.in_progress || 0}</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">OS Concluídas</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-3xl font-bold text-green-600">{stats?.orders_by_status?.completed || 0}</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Aguardando Peças</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-3xl font-bold text-yellow-600">{stats?.orders_by_status?.waiting_parts || 0}</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Estoque Baixo</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-2">
              <AlertTriangle className="h-5 w-5 text-yellow-500" />
              <p className="text-3xl font-bold text-yellow-600">{stats?.low_stock_count || 0}</p>
            </div>
          </CardContent>
        </Card>
      </div>

      <div className="grid gap-4 md:grid-cols-2">
        {revenueData.length > 0 && (
          <Card>
            <CardHeader>
              <CardTitle className="text-base">Receita vs Despesa (12 meses)</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="h-[300px]">
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart data={revenueData} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="name" fontSize={12} tickLine={false} />
                    <YAxis fontSize={12} tickLine={false} tickFormatter={(value) => `R$ ${(value / 1000).toFixed(0)}k`} />
                    <Tooltip formatter={(value) => formatCurrency(Number(value))} />
                    <Legend />
                    <Bar dataKey="Receita" fill="#22c55e" radius={[4, 4, 0, 0]} />
                    <Bar dataKey="Despesa" fill="#ef4444" radius={[4, 4, 0, 0]} />
                  </BarChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>
        )}

        {ordersData.length > 0 && (
          <Card>
            <CardHeader>
              <CardTitle className="text-base">OS Criadas por Mês</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="h-[300px]">
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart data={ordersData} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="name" fontSize={12} tickLine={false} />
                    <YAxis fontSize={12} tickLine={false} />
                    <Tooltip />
                    <Bar dataKey="OS" fill="#3b82f6" radius={[4, 4, 0, 0]} />
                  </BarChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>
        )}
      </div>

      <div className="grid gap-4 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Últimas Ordens de Serviço</CardTitle>
          </CardHeader>
          <CardContent>
            {recentOrders.length > 0 ? (
              <div className="space-y-3">
                {recentOrders.slice(0, 8).map((order: any) => (
                  <div key={order.id} className="flex items-center justify-between border-b pb-2 last:border-0">
                    <div>
                      <p className="font-mono font-medium text-sm">{order.order_number}</p>
                      <p className="text-xs text-muted-foreground">{order.client?.name || 'Sem cliente'}</p>
                    </div>
                    <div className="flex items-center gap-2">
                      <span className="text-xs text-muted-foreground">{formatDate(order.created_at)}</span>
                      <Badge variant={statusLabels[order.status]?.variant || 'default'}>
                        {statusLabels[order.status]?.label || order.status}
                      </Badge>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-sm text-muted-foreground text-center p-4">Nenhuma OS encontrada</p>
            )}
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">Itens com Estoque Baixo</CardTitle>
          </CardHeader>
          <CardContent>
            {lowStockItems.length > 0 ? (
              <div className="space-y-3">
                {lowStockItems.slice(0, 8).map((item: any) => (
                  <div key={item.id} className="flex items-center justify-between border-b pb-2 last:border-0">
                    <div className="flex items-center gap-2">
                      <Package className="h-4 w-4 text-yellow-500" />
                      <div>
                        <p className="font-medium text-sm">{item.name}</p>
                        <p className="text-xs text-muted-foreground">{item.category?.name || 'Sem categoria'}</p>
                      </div>
                    </div>
                    <div className="text-right">
                      <p className="text-sm font-bold text-yellow-600">{item.quantity} un</p>
                      <p className="text-xs text-muted-foreground">Mín: {item.minimum_quantity}</p>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-sm text-muted-foreground text-center p-4">Nenhum item com estoque baixo</p>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
