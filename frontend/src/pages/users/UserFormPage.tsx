import { useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useMutation, useQuery } from '@tanstack/react-query';
import { Loader2, ArrowLeft } from 'lucide-react';
import { usersApi } from '@/api/users';
import { userSchema, type UserFormData } from '@/utils/validators';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Button } from '@/components/ui/button';
import { useToast } from '@/components/ui/toast';

export function UserFormPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const isEditing = !!id;

  const { data: user, isLoading: loadingUser } = useQuery({
    queryKey: ['user', id],
    queryFn: () => usersApi.getById(Number(id)),
    enabled: isEditing,
  });

  const { data: roles } = useQuery({
    queryKey: ['roles'],
    queryFn: usersApi.listRoles,
  });

  const {
    register,
    handleSubmit,
    setValue,
    watch,
    formState: { errors },
  } = useForm<UserFormData>({
    resolver: zodResolver(userSchema) as any,
    defaultValues: { is_active: true },
  });

  const isActive = watch('is_active');

  useEffect(() => {
    if (user) {
      setValue('name', user.name);
      setValue('email', user.email);
      setValue('phone', user.phone);
      setValue('role_id', user.role?.id);
      setValue('is_active', user.is_active);
    }
  }, [user, setValue]);

  const mutation = useMutation({
    mutationFn: (data: UserFormData) =>
      isEditing ? usersApi.update(Number(id), data) : usersApi.create(data),
    onSuccess: () => {
      toast('success', isEditing ? 'Usuário atualizado' : 'Usuário criado');
      navigate('/users');
    },
    onError: () => {
      toast('error', 'Erro ao salvar usuário');
    },
  });

  const roleOptions = (roles || []).map((r) => ({ value: r.id, label: r.name }));

  if (isEditing && loadingUser) {
    return <div className="flex justify-center p-8"><Loader2 className="h-8 w-8 animate-spin" /></div>;
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" onClick={() => navigate('/users')}>
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <div>
          <h1 className="text-2xl font-bold">{isEditing ? 'Editar Usuário' : 'Novo Usuário'}</h1>
          <p className="text-muted-foreground">Preencha os dados do usuário</p>
        </div>
      </div>

      <form onSubmit={handleSubmit((data) => mutation.mutate(data))}>
        <Card>
          <CardHeader>
            <CardTitle>Dados do Usuário</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <Input label="Nome *" error={errors.name?.message} {...register('name')} />
            <div className="grid gap-4 md:grid-cols-2">
              <Input label="E-mail *" type="email" error={errors.email?.message} {...register('email')} />
              <Input label="Telefone" {...register('phone')} />
            </div>
            <Select
              label="Perfil *"
              options={roleOptions}
              placeholder="Selecione o perfil"
              error={errors.role_id?.message}
              {...register('role_id', { valueAsNumber: true })}
            />
            {!isEditing && (
              <div className="grid gap-4 md:grid-cols-2">
                <Input
                  label="Senha *"
                  type="password"
                  error={errors.password?.message}
                  {...register('password')}
                />
                <Input
                  label="Confirmar Senha"
                  type="password"
                  error={(errors as any).password_confirmation?.message}
                  {...register('password_confirmation')}
                />
              </div>
            )}
            <Switch
              label="Usuário Ativo"
              checked={isActive ?? true}
              onCheckedChange={(checked) => setValue('is_active', checked)}
            />
          </CardContent>
        </Card>

        <div className="flex justify-end gap-4 mt-6">
          <Button type="button" variant="outline" onClick={() => navigate('/users')}>
            Cancelar
          </Button>
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
            {isEditing ? 'Salvar' : 'Criar'}
          </Button>
        </div>
      </form>
    </div>
  );
}
