import { useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useMutation, useQuery } from '@tanstack/react-query';
import { Loader2, ArrowLeft } from 'lucide-react';
import { financialApi } from '@/api/financial';
import { transactionSchema, type TransactionFormData } from '@/utils/validators';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { useToast } from '@/components/ui/toast';

export function TransactionFormPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const isEditing = !!id;

  const { data: transaction, isLoading: loadingTransaction } = useQuery({
    queryKey: ['transaction', id],
    queryFn: () => financialApi.getTransactionById(Number(id)),
    enabled: isEditing,
  });

  const { data: categories } = useQuery({
    queryKey: ['financial-categories'],
    queryFn: financialApi.listCategories,
  });

  const {
    register,
    handleSubmit,
    setValue,
    formState: { errors },
  } = useForm<TransactionFormData>({
    resolver: zodResolver(transactionSchema) as any,
    defaultValues: { status: 'pending' },
  });

  useEffect(() => {
    if (transaction) {
      setValue('type', transaction.type as 'income' | 'expense');
      setValue('category_id', transaction.category?.id ?? null);
      setValue('service_order_id', transaction.service_order?.id ?? null);
      setValue('description', transaction.description);
      setValue('amount', transaction.amount);
      setValue('payment_method', transaction.payment_method);
      setValue('payment_date', transaction.payment_date);
      setValue('due_date', transaction.due_date);
      setValue('status', transaction.status as 'pending' | 'paid' | 'cancelled');
    }
  }, [transaction, setValue]);

  const mutation = useMutation({
    mutationFn: (data: TransactionFormData) =>
      isEditing ? financialApi.updateTransaction(Number(id), data) : financialApi.createTransaction(data),
    onSuccess: () => {
      toast('success', isEditing ? 'Transação atualizada' : 'Transação criada');
      navigate('/financial/transactions');
    },
    onError: () => {
      toast('error', 'Erro ao salvar transação');
    },
  });

  const categoryOptions = (categories || []).map((c) => ({ value: c.id, label: c.name }));

  if (isEditing && loadingTransaction) {
    return <div className="flex justify-center p-8"><Loader2 className="h-8 w-8 animate-spin" /></div>;
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" onClick={() => navigate('/financial/transactions')}>
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <div>
          <h1 className="text-2xl font-bold">{isEditing ? 'Editar Transação' : 'Nova Transação'}</h1>
          <p className="text-muted-foreground">Preencha os dados da transação</p>
        </div>
      </div>

      <form onSubmit={handleSubmit((data) => mutation.mutate(data))}>
        <Card>
          <CardHeader>
            <CardTitle>Dados da Transação</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid gap-4 md:grid-cols-2">
              <Select
                label="Tipo *"
                options={[
                  { value: 'income', label: 'Receita' },
                  { value: 'expense', label: 'Despesa' },
                ]}
                error={errors.type?.message}
                {...register('type')}
              />
              <Select
                label="Categoria"
                options={categoryOptions}
                placeholder="Selecione"
                {...register('category_id', { valueAsNumber: true })}
              />
            </div>
            <Input
              label="Descrição *"
              error={errors.description?.message}
              {...register('description')}
            />
            <div className="grid gap-4 md:grid-cols-2">
              <Input
                label="Valor *"
                type="number"
                step="0.01"
                error={errors.amount?.message}
                {...register('amount', { valueAsNumber: true })}
              />
              <Select
                label="Forma de Pagamento *"
                options={[
                  { value: 'cash', label: 'Dinheiro' },
                  { value: 'credit_card', label: 'Cartão de Crédito' },
                  { value: 'debit_card', label: 'Cartão de Débito' },
                  { value: 'pix', label: 'PIX' },
                  { value: 'bank_transfer', label: 'Transferência Bancária' },
                  { value: 'boleto', label: 'Boleto' },
                ]}
                error={errors.payment_method?.message}
                {...register('payment_method')}
              />
            </div>
            <div className="grid gap-4 md:grid-cols-2">
              <Input
                label="Data de Pagamento *"
                type="date"
                error={errors.payment_date?.message}
                {...register('payment_date')}
              />
              <Input
                label="Data de Vencimento"
                type="date"
                {...register('due_date')}
              />
            </div>
            <Select
              label="Status *"
              options={[
                { value: 'pending', label: 'Pendente' },
                { value: 'paid', label: 'Pago' },
                { value: 'cancelled', label: 'Cancelado' },
              ]}
              error={errors.status?.message}
              {...register('status')}
            />
          </CardContent>
        </Card>

        <div className="flex justify-end gap-4 mt-6">
          <Button type="button" variant="outline" onClick={() => navigate('/financial/transactions')}>
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
