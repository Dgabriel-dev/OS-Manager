export interface User {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  role: Role;
  is_active: boolean;
  avatar: string | null;
  created_at: string;
}

export interface Role {
  id: number;
  name: string;
  description: string | null;
}

export interface Client {
  id: number;
  name: string;
  cpf_cnpj: string;
  phone: string;
  whatsapp: string | null;
  email: string | null;
  cep: string | null;
  address: string | null;
  city: string | null;
  state: string | null;
  observations: string | null;
  orders_count?: number;
  created_at: string;
}

export interface Equipment {
  id: number;
  client: Client;
  category: string;
  brand: string | null;
  model: string | null;
  serial_number: string | null;
  color: string | null;
  accessories_delivered: string | null;
  physical_state: string | null;
  reported_defect: string | null;
  technical_diagnosis: string | null;
  files: EquipmentFile[];
  created_at: string;
}

export interface EquipmentFile {
  id: number;
  file_path: string;
  file_type: string;
  original_name: string;
}

export interface ServiceOrder {
  id: number;
  order_number: string;
  client: Client;
  equipment: Equipment;
  technician: User | null;
  priority: string;
  status: string;
  estimated_value: number | null;
  final_value: number | null;
  warranty_days: number;
  entry_date: string;
  estimated_delivery_date: string | null;
  delivered_at: string | null;
  notes: string | null;
  internal_notes: string | null;
  items: OrderItem[];
  created_at: string;
}

export interface OrderItem {
  id: number;
  stock_item?: StockItem;
  description?: string;
  quantity: number;
  unit_price: number;
  total_price: number;
  type?: string;
}

export interface OrderHistory {
  id: number;
  user: User | null;
  action: string;
  old_values: Record<string, any> | null;
  new_values: Record<string, any> | null;
  created_at: string;
}

export interface StockItem {
  id: number;
  name: string;
  internal_code: string | null;
  barcode: string | null;
  category: StockCategory | null;
  supplier: string | null;
  purchase_price: number;
  sale_price: number;
  quantity: number;
  minimum_quantity: number;
  location: string | null;
  is_low_stock: boolean;
  created_at: string;
}

export interface StockCategory {
  id: number;
  name: string;
  description: string | null;
}

export interface StockMovement {
  id: number;
  stock_item: StockItem;
  type: string;
  quantity: number;
  previous_quantity: number;
  new_quantity: number;
  user: User | null;
  notes: string | null;
  created_at: string;
}

export interface Transaction {
  id: number;
  type: string;
  category: FinancialCategory | null;
  service_order: ServiceOrder | null;
  description: string;
  amount: number;
  payment_method: string;
  payment_date: string;
  due_date: string | null;
  status: string;
  user: User | null;
  created_at: string;
}

export interface FinancialCategory {
  id: number;
  name: string;
  type: string;
}

export interface Sale {
  id: number;
  client: Client | null;
  user: User | null;
  total_amount: number;
  total_cost: number;
  profit: number;
  payment_method: string | null;
  payment_status: 'pending' | 'paid' | 'cancelled';
  notes: string | null;
  items: SaleItem[];
  created_at: string;
  updated_at: string;
}

export interface SaleItem {
  id: number;
  name: string;
  category: SaleCategory | null;
  quantity: number;
  unit_price: number;
  cost_price: number;
  total_price: number;
}

export interface SaleCategory {
  id: number;
  name: string;
  created_at: string;
}

export interface DashboardStats {
  total_clients: number;
  total_equipment: number;
  orders_by_status: {
    open: number;
    in_progress: number;
    waiting_parts: number;
    completed: number;
    delivered: number;
    cancelled: number;
  };
  monthly_revenue: number;
  low_stock_count: number;
}

export interface Notification {
  id: number;
  title: string;
  message: string;
  read_at: string | null;
  data: any;
  created_at: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
}
