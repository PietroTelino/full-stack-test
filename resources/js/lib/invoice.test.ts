import { describe, expect, it } from 'vitest';
import { getInvoiceStatusColor } from './invoice';

describe('getInvoiceStatusColor', () => {
  it('returns the correct variant for each known status', () => {
    expect(getInvoiceStatusColor('draft')).toBe('default');
    expect(getInvoiceStatusColor('pending')).toBe('secondary');
    expect(getInvoiceStatusColor('issued')).toBe('secondary');
    expect(getInvoiceStatusColor('paid')).toBe('default');
    expect(getInvoiceStatusColor('overdue')).toBe('destructive');
    expect(getInvoiceStatusColor('cancelled')).toBe('outline');
  });

  it('falls back to default for unknown status', () => {
    expect(getInvoiceStatusColor('unknown')).toBe('default');
  });
});
