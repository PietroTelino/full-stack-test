import { describe, expect, it } from 'vitest';
import { validateCustomerForm, type CustomerValidationInput } from './customer';

const buildInput = (overrides: Partial<CustomerValidationInput> = {}): CustomerValidationInput => ({
  email: 'john@example.com',
  phone: '+55 (11) 91234-5678',
  document: '529.982.247-25',
  ...overrides,
});

describe('validateCustomerForm', () => {
  it('passes with valid data', () => {
    const errors = validateCustomerForm(buildInput());
    expect(errors).toEqual({});
  });

  it('validates email format', () => {
    const errors = validateCustomerForm(buildInput({ email: 'invalid-email' }));
    expect(errors.email).toBe('Invalid email format.');
  });

  it('validates phone length when provided', () => {
    const tooShort = validateCustomerForm(buildInput({ phone: '123456789' }));
    const tooLong = validateCustomerForm(buildInput({ phone: '+55 111 2345 67890' }));

    expect(tooShort.phone).toBe('Invalid phone number.');
    expect(tooLong.phone).toBe('Invalid phone number.');
  });

  it('validates CPF/CNPJ when provided', () => {
    const errors = validateCustomerForm(buildInput({ document: '111.111.111-11' }));
    expect(errors.document).toBe('Invalid CPF or CNPJ.');
  });
});
