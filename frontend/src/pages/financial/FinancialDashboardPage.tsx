import { useQuery } from '@tanstack/react-query';
import { DollarSign, TrendingUp, TrendingDown, Clock } from 'lucide-react';
import { financialApi } from '@/api/financial';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { StatCard } from '@/components/ui/stat-card';
import { LoadingPage } from '@/components/ui/loading-spinner';
import { formatCurrency } from '@/utils/formatters';

export function FinancialDashboardPage() {
  const { data: summary, isLoading } = useQuery({
    queryKey: ['financial-summary'],
    queryFn: financialApi.getSummary,
  });

  if (isLoading) return <LoadingPage />;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Financeiro</h1>
        <p className="text-muted-foreground">Visão geral das finanças</p>
      </div>

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <StatCard
          icon={<TrendingUp className="h-5 w-5" />}
          label="Receitas"
          value={formatCurrency(summary?.total_income || 0)}
        />
        <StatCard
          icon={<TrendingDown className="h-5 w-5" />}
          label="Despesas"
          value={formatCurrency(summary?.total_expenses || 0)}
        />
        <StatCard
          icon={<DollarSign className="h-5 w-5" />}
          label="Saldo"
          value={formatCurrency(summary?.balance || 0)}
        />
        <StatCard
          icon={<Clock className="h-5 w-5" />}
          label="Recebimentos Pendentes"
          value={formatCurrency(summary?.pending_receivables || 0)}
        />
      </div>

      <div className="grid gap-4 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle className="text-base">A Pagar</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-bold text-red-600">
              {formatCurrency(summary?.pending_payables || 0)}
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-base">A Receber</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-bold text-green-600">
              {formatCurrency(summary?.pending_receivables || 0)}
            </p>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
