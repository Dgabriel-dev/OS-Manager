import { z } from 'zod';

export const loginSchema = z.object({
  email: z.string().email('E-mail inválido'),
  password: z.string().min(6, 'Senha deve ter no mínimo 6 caracteres'),
});

export type LoginFormData = z.infer<typeof loginSchema>;

export const clientSchema = z.object({
  name: z.string().min(1, 'Nome é obrigatório'),
  cpf_cnpj: z.string().min(11, 'CPF/CNPJ inválido').max(18, 'CPF/CNPJ inválido'),
  phone: z.string().min(10, 'Telefone inválido').max(15, 'Telefone inválido'),
  whatsapp: z.string().optional().nullable().transform(v => v === '' ? null : v),
  email: z.string().optional().nullable().transform(v => v === '' ? null : v).refine(v => !v || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v), 'E-mail inválido'),
  cep: z.string().optional().nullable().transform(v => v === '' ? null : v),
  address: z.string().optional().nullable().transform(v => v === '' ? null : v),
  city: z.string().optional().nullable().transform(v => v === '' ? null : v),
  state: z.string().optional().nullable().transform(v => v === '' ? null : v),
  observations: z.string().optional().nullable().transform(v => v === '' ? null : v),
});

export type ClientFormData = z.infer<typeof clientSchema>;

export const equipmentSchema = z.object({
  client_id: z.number().min(1, 'Cliente é obrigatório'),
  category: z.string().min(1, 'Categoria é obrigatória'),
  brand: z.string().optional().nullable(),
  model: z.string().optional().nullable(),
  serial_number: z.string().optional().nullable(),
  color: z.string().optional().nullable(),
  accessories_delivered: z.string().optional().nullable(),
  physical_state: z.string().optional().nullable(),
  reported_defect: z.string().optional().nullable(),
  technical_diagnosis: z.string().optional().nullable(),
});

export type EquipmentFormData = z.infer<typeof equipmentSchema>;

export const serviceOrderItemSchema = z.object({
  description: z.string().min(1, 'Descrição é obrigatória'),
  quantity: z.number().min(1, 'Quantidade deve ser maior que 0'),
  unit_price: z.number().min(0, 'Preço inválido'),
  type: z.enum(['service', 'part', 'other']),
});

export type ServiceOrderItemFormData = z.infer<typeof serviceOrderItemSchema>;

export const serviceOrderSchema = z.object({
  client_id: z.number().min(1, 'Cliente é obrigatório'),
  equipment_id: z.number().min(1, 'Equipamento é obrigatório'),
  technician_id: z.number().optional().nullable(),
  priority: z.enum(['low', 'medium', 'high', 'urgent'], {
    errorMap: () => ({ message: 'Prioridade é obrigatória' }),
  }),
  estimated_value: z.number().optional().nullable(),
  warranty_days: z.number().min(0, 'Valor inválido').optional(),
  estimated_delivery_date: z.string().optional().nullable(),
  notes: z.string().optional().nullable(),
  internal_notes: z.string().optional().nullable(),
  items: z.array(serviceOrderItemSchema).optional(),
});

export type ServiceOrderFormData = z.infer<typeof serviceOrderSchema>;

export const stockItemSchema = z.object({
  name: z.string().min(1, 'Nome é obrigatório'),
  internal_code: z.string().optional().nullable(),
  barcode: z.string().optional().nullable(),
  category_id: z.number().optional().nullable(),
  supplier: z.string().optional().nullable(),
  purchase_price: z.number().min(0, 'Preço inválido'),
  sale_price: z.number().min(0, 'Preço inválido'),
  quantity: z.number().min(0, 'Quantidade inválida'),
  minimum_quantity: z.number().min(0, 'Quantidade inválida'),
  location: z.string().optional().nullable(),
});

export type StockItemFormData = z.infer<typeof stockItemSchema>;

export const stockMovementSchema = z.object({
  stock_item_id: z.number().min(1, 'Item é obrigatório'),
  type: z.enum(['entry', 'exit'], {
    errorMap: () => ({ message: 'Tipo é obrigatório' }),
  }),
  quantity: z.number().min(1, 'Quantidade deve ser maior que 0'),
  notes: z.string().optional().nullable(),
});

export type StockMovementFormData = z.infer<typeof stockMovementSchema>;

export const transactionSchema = z.object({
  type: z.enum(['income', 'expense'], {
    errorMap: () => ({ message: 'Tipo é obrigatório' }),
  }),
  category_id: z.number().optional().nullable(),
  service_order_id: z.number().optional().nullable(),
  description: z.string().min(1, 'Descrição é obrigatória'),
  amount: z.number().min(0.01, 'Valor deve ser maior que 0'),
  payment_method: z.string().min(1, 'Forma de pagamento é obrigatória'),
  payment_date: z.string().min(1, 'Data é obrigatória'),
  due_date: z.string().optional().nullable(),
  status: z.enum(['pending', 'paid', 'cancelled'], {
    errorMap: () => ({ message: 'Status é obrigatório' }),
  }),
});

export type TransactionFormData = z.infer<typeof transactionSchema>;

export const userSchema = z.object({
  name: z.string().min(1, 'Nome é obrigatório'),
  email: z.string().email('E-mail inválido'),
  phone: z.string().optional().nullable().transform(v => v === '' ? null : v),
  role_id: z.number().min(1, 'Perfil é obrigatório'),
  is_active: z.boolean().optional(),
  password: z.string().min(6, 'Senha deve ter no mínimo 6 caracteres').optional(),
  password_confirmation: z.string().optional(),
}).refine((data) => {
  if (data.password && data.password !== data.password_confirmation) {
    return false;
  }
  return true;
}, {
  message: 'Senhas não conferem',
  path: ['password_confirmation'],
});

export type UserFormData = z.infer<typeof userSchema>;

export const profileSchema = z.object({
  name: z.string().min(1, 'Nome é obrigatório'),
  email: z.string().email('E-mail inválido'),
  phone: z.string().optional().nullable().transform(v => v === '' ? null : v),
});

export type ProfileFormData = z.infer<typeof profileSchema>;

export const changePasswordSchema = z.object({
  current_password: z.string().min(1, 'Senha atual é obrigatória'),
  password: z.string().min(6, 'Nova senha deve ter no mínimo 6 caracteres'),
  password_confirmation: z.string(),
}).refine((data) => data.password === data.password_confirmation, {
  message: 'Senhas não conferem',
  path: ['password_confirmation'],
});

export type ChangePasswordFormData = z.infer<typeof changePasswordSchema>;
