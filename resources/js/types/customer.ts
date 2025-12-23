import type { InvoiceSummary } from './invoice';

export interface Customer {
  id: number;
  name: string;
  email: string;
  phone?: string;
  address?: string;
  document?: string;
  created_at: string;
  invoices_count?: number;
  invoices?: InvoiceSummary[];
}
