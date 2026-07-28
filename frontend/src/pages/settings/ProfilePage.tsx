import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useMutation } from '@tanstack/react-query';
import { Loader2, User, Lock } from 'lucide-react';
import { authApi } from '@/api/auth';
import { useAuth } from '@/hooks/useAuth';
import { profileSchema, changePasswordSchema, type ProfileFormData, type ChangePasswordFormData } from '@/utils/validators';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useToast } from '@/components/ui/toast';
import { Avatar } from '@/components/ui/avatar';

export function ProfilePage() {
  const { user, updateUser } = useAuth();
  const { toast } = useToast();

  const {
    register: registerProfile,
    handleSubmit: handleSubmitProfile,
    setValue: setValueProfile,
    formState: { errors: profileErrors },
  } = useForm<ProfileFormData>({
    resolver: zodResolver(profileSchema),
  });

  const {
    register: registerPassword,
    handleSubmit: handleSubmitPassword,
    reset: resetPassword,
    formState: { errors: passwordErrors },
  } = useForm<ChangePasswordFormData>({
    resolver: zodResolver(changePasswordSchema),
  });

  useEffect(() => {
    if (user) {
      setValueProfile('name', user.name);
      setValueProfile('email', user.email);
      setValueProfile('phone', user.phone);
    }
  }, [user, setValueProfile]);

  const profileMutation = useMutation({
    mutationFn: (data: ProfileFormData) => authApi.updateProfile(data),
    onSuccess: (data) => {
      updateUser(data);
      toast('success', 'Perfil atualizado');
    },
    onError: () => {
      toast('error', 'Erro ao atualizar perfil');
    },
  });

  const passwordMutation = useMutation({
    mutationFn: (data: ChangePasswordFormData) => authApi.changePassword(data),
    onSuccess: () => {
      resetPassword();
      toast('success', 'Senha alterada');
    },
    onError: () => {
      toast('error', 'Erro ao alterar senha');
    },
  });

  return (
    <div className="space-y-6 max-w-2xl">
      <div>
        <h1 className="text-2xl font-bold">Meu Perfil</h1>
        <p className="text-muted-foreground">Gerencie suas configurações</p>
      </div>

      <div className="flex items-center gap-4">
        <Avatar src={user?.avatar} fallback={user?.name?.charAt(0)} size="lg" />
        <div>
          <h2 className="font-semibold">{user?.name}</h2>
          <p className="text-sm text-muted-foreground">{user?.email}</p>
        </div>
      </div>

      <Tabs defaultValue="profile">
        <TabsList>
          <TabsTrigger value="profile">
            <User className="mr-2 h-4 w-4" />
            Perfil
          </TabsTrigger>
          <TabsTrigger value="password">
            <Lock className="mr-2 h-4 w-4" />
            Senha
          </TabsTrigger>
        </TabsList>

        <TabsContent value="profile">
          <Card>
            <CardHeader>
              <CardTitle>Informações Pessoais</CardTitle>
              <CardDescription>Atualize suas informações pessoais</CardDescription>
            </CardHeader>
            <CardContent>
              <form onSubmit={handleSubmitProfile((data) => profileMutation.mutate(data))} className="space-y-4">
                <Input
                  label="Nome"
                  error={profileErrors.name?.message}
                  {...registerProfile('name')}
                />
                <Input
                  label="E-mail"
                  type="email"
                  error={profileErrors.email?.message}
                  {...registerProfile('email')}
                />
                <Input
                  label="Telefone"
                  {...registerProfile('phone')}
                />
                <Button type="submit" disabled={profileMutation.isPending}>
                  {profileMutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                  Salvar
                </Button>
              </form>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="password">
          <Card>
            <CardHeader>
              <CardTitle>Alterar Senha</CardTitle>
              <CardDescription>Atualize sua senha de acesso</CardDescription>
            </CardHeader>
            <CardContent>
              <form onSubmit={handleSubmitPassword((data) => passwordMutation.mutate(data))} className="space-y-4">
                <Input
                  label="Senha Atual"
                  type="password"
                  error={passwordErrors.current_password?.message}
                  {...registerPassword('current_password')}
                />
                <Input
                  label="Nova Senha"
                  type="password"
                  error={passwordErrors.password?.message}
                  {...registerPassword('password')}
                />
                <Input
                  label="Confirmar Nova Senha"
                  type="password"
                  error={passwordErrors.password_confirmation?.message}
                  {...registerPassword('password_confirmation')}
                />
                <Button type="submit" disabled={passwordMutation.isPending}>
                  {passwordMutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                  Alterar Senha
                </Button>
              </form>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
}
