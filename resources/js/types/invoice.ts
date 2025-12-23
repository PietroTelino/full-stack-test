export type InvoiceStatus =
  | 'draft'
  | 'pending'
  | 'issued'
  | 'paid'
  | 'overdue'
  | 'cancelled';

export interface InvoiceItem {
  id?: number;
  title: string;
  subtitle?: string;
  quantity: number;
  unit_price: number;
  amount?: number;
}

export interface InvoiceSummary {
  id: number;
  code: string;
  amount: number;
  status: InvoiceStatus;
  issue_date: string;
  due_date: string;
  payment_date?: string;
}

export interface BankBilletSummary {
  id: number;
  code: string;
  barcode?: string;
  expires_at?: string;
}

export interface InvoiceWithItems extends InvoiceSummary {
  customer?: {
    id: number;
    name: string;
    email: string;
    phone?: string;
    address?: string;
  };
  invoice_items: InvoiceItem[];
  bank_billet?: BankBilletSummary;
}
