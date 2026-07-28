import { BrowserRouter, Routes, Route, Navigate, Outlet } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { AuthLayout } from '@/layouts/AuthLayout';
import { DashboardLayout } from '@/layouts/DashboardLayout';
import { LoginPage } from '@/pages/auth/LoginPage';
import { DashboardPage } from '@/pages/DashboardPage';
import { ClientListPage } from '@/pages/clients/ClientListPage';
import { ClientFormPage } from '@/pages/clients/ClientFormPage';
import { EquipmentListPage } from '@/pages/equipment/EquipmentListPage';
import { EquipmentFormPage } from '@/pages/equipment/EquipmentFormPage';
import { ServiceOrderListPage } from '@/pages/service-orders/ServiceOrderListPage';
import { ServiceOrderFormPage } from '@/pages/service-orders/ServiceOrderFormPage';
import { ServiceOrderDetailPage } from '@/pages/service-orders/ServiceOrderDetailPage';
import { StockItemListPage } from '@/pages/stock/StockItemListPage';
import { StockItemFormPage } from '@/pages/stock/StockItemFormPage';
import { StockMovementPage } from '@/pages/stock/StockMovementPage';
import { TransactionListPage } from '@/pages/financial/TransactionListPage';
import { TransactionFormPage } from '@/pages/financial/TransactionFormPage';
import { FinancialDashboardPage } from '@/pages/financial/FinancialDashboardPage';
import { UserListPage } from '@/pages/users/UserListPage';
import { UserFormPage } from '@/pages/users/UserFormPage';
import { NotificationListPage } from '@/pages/notifications/NotificationListPage';
import { ProfilePage } from '@/pages/settings/ProfilePage';
import { LoadingPage } from '@/components/ui/loading-spinner';

function ProtectedRoute() {
  const { isAuthenticated, isLoading } = useAuth();

  if (isLoading) return <LoadingPage />;
  if (!isAuthenticated) return <Navigate to="/login" replace />;

  return <Outlet />;
}

function PublicRoute() {
  const { isAuthenticated, isLoading } = useAuth();

  if (isLoading) return <LoadingPage />;
  if (isAuthenticated) return <Navigate to="/dashboard" replace />;

  return <Outlet />;
}

export function AppRouter() {
  return (
    <BrowserRouter>
      <Routes>
        <Route element={<PublicRoute />}>
          <Route element={<AuthLayout />}>
            <Route path="/login" element={<LoginPage />} />
          </Route>
        </Route>

        <Route element={<ProtectedRoute />}>
          <Route element={<DashboardLayout />}>
            <Route path="/dashboard" element={<DashboardPage />} />
            <Route path="/clients" element={<ClientListPage />} />
            <Route path="/clients/new" element={<ClientFormPage />} />
            <Route path="/clients/:id" element={<ClientFormPage />} />
            <Route path="/clients/:id/edit" element={<ClientFormPage />} />
            <Route path="/equipment" element={<EquipmentListPage />} />
            <Route path="/equipment/new" element={<EquipmentFormPage />} />
            <Route path="/equipment/:id" element={<EquipmentFormPage />} />
            <Route path="/equipment/:id/edit" element={<EquipmentFormPage />} />
            <Route path="/service-orders" element={<ServiceOrderListPage />} />
            <Route path="/service-orders/new" element={<ServiceOrderFormPage />} />
            <Route path="/service-orders/:id" element={<ServiceOrderDetailPage />} />
            <Route path="/service-orders/:id/edit" element={<ServiceOrderFormPage />} />
            <Route path="/stock" element={<StockItemListPage />} />
            <Route path="/stock/new" element={<StockItemFormPage />} />
            <Route path="/stock/:id" element={<StockItemFormPage />} />
            <Route path="/stock/:id/edit" element={<StockItemFormPage />} />
            <Route path="/stock/movements" element={<StockMovementPage />} />
            <Route path="/financial" element={<FinancialDashboardPage />} />
            <Route path="/financial/transactions" element={<TransactionListPage />} />
            <Route path="/financial/new" element={<TransactionFormPage />} />
            <Route path="/financial/:id" element={<TransactionFormPage />} />
            <Route path="/financial/:id/edit" element={<TransactionFormPage />} />
            <Route path="/users" element={<UserListPage />} />
            <Route path="/users/new" element={<UserFormPage />} />
            <Route path="/users/:id" element={<UserFormPage />} />
            <Route path="/users/:id/edit" element={<UserFormPage />} />
            <Route path="/notifications" element={<NotificationListPage />} />
            <Route path="/settings" element={<ProfilePage />} />
          </Route>
        </Route>

        <Route path="*" element={<Navigate to="/dashboard" replace />} />
      </Routes>
    </BrowserRouter>
  );
}
