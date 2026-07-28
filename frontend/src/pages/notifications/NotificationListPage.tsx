import { useQuery } from '@tanstack/react-query';
import { Bell, Check } from 'lucide-react';
import { notificationsApi } from '@/api/notifications';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { LoadingPage } from '@/components/ui/loading-spinner';
import { formatDateTime } from '@/utils/formatters';

export function NotificationListPage() {
  const { data, isLoading, refetch } = useQuery({
    queryKey: ['notifications'],
    queryFn: () => notificationsApi.list(),
  });

  const handleMarkAllRead = async () => {
    await notificationsApi.markAllAsRead();
    refetch();
  };

  const handleMarkRead = async (id: number) => {
    await notificationsApi.markAsRead(id);
    refetch();
  };

  if (isLoading) return <LoadingPage />;

  const notifications = data?.data || [];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Notificações</h1>
          <p className="text-muted-foreground">Suas notificações</p>
        </div>
        {notifications.some((n) => !n.read_at) && (
          <Button variant="outline" onClick={handleMarkAllRead}>
            <Check className="mr-2 h-4 w-4" />
            Marcar todas como lidas
          </Button>
        )}
      </div>

      <div className="space-y-4">
        {notifications.length === 0 ? (
          <Card>
            <CardContent className="flex flex-col items-center justify-center py-12">
              <Bell className="h-12 w-12 text-muted-foreground mb-4" />
              <p className="text-muted-foreground">Nenhuma notificação</p>
            </CardContent>
          </Card>
        ) : (
          notifications.map((notification) => (
            <Card
              key={notification.id}
              className={!notification.read_at ? 'border-l-4 border-l-primary' : ''}
            >
              <CardContent className="flex items-start justify-between p-4">
                <div className="space-y-1">
                  <h3 className="font-medium">{notification.title}</h3>
                  <p className="text-sm text-muted-foreground">{notification.message}</p>
                  <p className="text-xs text-muted-foreground">{formatDateTime(notification.created_at)}</p>
                </div>
                {!notification.read_at && (
                  <Button
                    variant="ghost"
                    size="icon"
                    onClick={() => handleMarkRead(notification.id)}
                  >
                    <Check className="h-4 w-4" />
                  </Button>
                )}
              </CardContent>
            </Card>
          ))
        )}
      </div>
    </div>
  );
}
