import { Outlet } from 'react-router-dom';
import { Wrench } from 'lucide-react';

export function AuthLayout() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-muted/50 p-4">
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <div className="flex items-center justify-center gap-2 mb-2">
            <Wrench className="h-10 w-10 text-primary" />
            <h1 className="text-2xl font-bold">OS Manager</h1>
          </div>
          <p className="text-muted-foreground">Sistema de Ordens de Serviço</p>
        </div>
        <Outlet />
      </div>
    </div>
  );
}
