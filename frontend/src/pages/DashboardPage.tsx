import { useQuery } from '@tanstack/react-query';
import { Users, Monitor, FileText, Package, DollarSign, AlertTriangle } from 'lucide-react';
import { dashboardApi } from '@/api/dashboard';
import { StatCard } from '@/components/ui/stat-card';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { LoadingPage } from '@/components/ui/loading-spinner';
import { formatCurrency } from '@/utils/formatters';

export function DashboardPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['dashboard-stats'],
    queryFn: dashboardApi.getStats,
  });

  if (isLoading) return <LoadingPage />;

  const stats = data?.stats;

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

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
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
    </div>
  );
}
