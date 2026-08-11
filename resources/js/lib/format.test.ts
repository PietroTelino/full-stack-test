import { describe, expect, it } from 'vitest';
import { formatCurrency, formatDate } from './format';

const normalizeSpaces = (s: string) => s.replace(/\u00A0/g, ' ');

describe('formatCurrency', () => {
  it('formats cents to a currency string using defaults', () => {
    const result = formatCurrency(12345);

    expect(typeof result).toBe('string');

    const normalized = normalizeSpaces(result);
    expect(
      normalized.includes('123.45') || normalized.includes('123,45')
    ).toBe(true);
  });

  it('supports custom locale and currency', () => {
    const result = formatCurrency(12345, 'pt-BR', 'BRL');
    expect(normalizeSpaces(result)).toBe('R$ 123,45');
  });
});

describe('formatDate', () => {
  it('formats ISO date string consistently in UTC', () => {
    const date = new Date('2025-01-15T12:00:00Z');

    const result = formatDate(
      date,
      { year: 'numeric', month: '2-digit', day: '2-digit', timeZone: 'UTC' } as Intl.DateTimeFormatOptions,
      'en-US'
    );

    expect(result).toBe('01/15/2025');
  });

  it('accepts Date instances', () => {
    const date = new Date('2025-01-15T12:00:00Z');
    const result = formatDate(date, { year: 'numeric', month: '2-digit', day: '2-digit' }, 'en-US');
    expect(result).toBe('01/15/2025');
  });
});
