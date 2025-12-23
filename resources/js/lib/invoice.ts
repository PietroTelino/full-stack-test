import type { InvoiceStatus } from '@/types';

const STATUS_COLORS: Record<InvoiceStatus, string> = {
  draft: 'default',
  pending: 'secondary',
  issued: 'secondary',
  paid: 'default',
  overdue: 'destructive',
  cancelled: 'outline',
};

export function getInvoiceStatusColor(status: InvoiceStatus): string {
  return STATUS_COLORS[status] ?? 'default';
}
