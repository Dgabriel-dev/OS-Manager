import { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import {
  LayoutDashboard,
  Users,
  Monitor,
  FileText,
  Package,
  DollarSign,
  UserCog,
  Settings,
  ChevronLeft,
  ChevronRight,
  Wrench,
  ShoppingCart,
} from 'lucide-react';
import { cn } from '@/utils/cn';
import { Button } from './button';
import { useAuth } from '@/hooks/useAuth';

interface SidebarProps {
  collapsed?: boolean;
  onToggle?: () => void;
}

interface NavItem {
  label: string;
  href: string;
  icon: React.ComponentType<any>;
  roles?: string[];
}

const navItems: NavItem[] = [
  { label: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
  { label: 'Clientes', href: '/clients', icon: Users },
  { label: 'Equipamentos', href: '/equipment', icon: Monitor },
  { label: 'Ordens de Serviço', href: '/service-orders', icon: FileText },
  { label: 'Estoque', href: '/stock', icon: Package },
  { label: 'Vendas', href: '/sales', icon: ShoppingCart },
  { label: 'Financeiro', href: '/financial', icon: DollarSign },
  { label: 'Usuários', href: '/users', icon: UserCog, roles: ['admin'] },
  { label: 'Configurações', href: '/settings', icon: Settings },
];

export function Sidebar({ collapsed = false, onToggle }: SidebarProps) {
  const location = useLocation();
  const { hasRole } = useAuth();

  const filteredItems = navItems.filter((item) => {
    if (!item.roles) return true;
    return item.roles.some((role) => hasRole(role));
  });

  return (
    <aside
      className={cn(
        'flex flex-col border-r bg-card transition-all duration-300',
        collapsed ? 'w-16' : 'w-64'
      )}
    >
      <div className="flex h-14 items-center border-b px-4">
        {!collapsed && (
          <div className="flex items-center gap-2">
            <Wrench className="h-6 w-6 text-primary" />
            <span className="font-semibold text-lg">OS Manager</span>
          </div>
        )}
        {collapsed && <Wrench className="h-6 w-6 text-primary mx-auto" />}
      </div>

      <nav className="flex-1 space-y-1 p-2">
        {filteredItems.map((item) => {
          const Icon = item.icon;
          const isActive = location.pathname.startsWith(item.href);
          return (
            <Link
              key={item.href}
              to={item.href}
              className={cn(
                'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                isActive
                  ? 'bg-primary text-primary-foreground'
                  : 'text-muted-foreground hover:bg-muted hover:text-foreground',
                collapsed && 'justify-center px-2'
              )}
            >
              <Icon className="h-5 w-5 shrink-0" />
              {!collapsed && <span>{item.label}</span>}
            </Link>
          );
        })}
      </nav>

      <div className="border-t p-2">
        <Button
          variant="ghost"
          size={collapsed ? 'icon' : 'default'}
          className={cn('w-full', !collapsed && 'justify-start gap-3')}
          onClick={onToggle}
        >
          {collapsed ? <ChevronRight className="h-4 w-4" /> : <ChevronLeft className="h-4 w-4" />}
          {!collapsed && <span>Recolher</span>}
        </Button>
      </div>
    </aside>
  );
}
