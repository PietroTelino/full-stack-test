export type CustomerValidationInput = {
    email: string;
    phone?: string | null;
    document?: string | null;
};

export type CustomerValidationErrors = Partial<Record<keyof CustomerValidationInput, string>>;

const onlyNumbers = (value: string) => value.replace(/\D/g, '');

const isValidEmail = (email: string): boolean => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
};

/**
 * Validação CPF (dígitos verificadores)
 */
const isValidCPF = (cpf: string): boolean => {
    cpf = onlyNumbers(cpf);

    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

    let sum = 0;
    let rest;

    for (let i = 1; i <= 9; i++) {
        sum += parseInt(cpf.substring(i - 1, i)) * (11 - i);
    }

    rest = (sum * 10) % 11;
    if (rest === 10 || rest === 11) rest = 0;
    if (rest !== parseInt(cpf.substring(9, 10))) return false;

    sum = 0;
    for (let i = 1; i <= 10; i++) {
        sum += parseInt(cpf.substring(i - 1, i)) * (12 - i);
    }

    rest = (sum * 10) % 11;
    if (rest === 10 || rest === 11) rest = 0;

    return rest === parseInt(cpf.substring(10, 11));
};

/**
 * Validação CNPJ (dígitos verificadores)
 */
const isValidCNPJ = (cnpj: string): boolean => {
    cnpj = onlyNumbers(cnpj);

    if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) return false;

    const validateDigit = (length: number) => {
        let sum = 0;
        let pos = length - 7;

        for (let i = length; i >= 1; i--) {
            sum += parseInt(cnpj.charAt(length - i)) * pos--;
            if (pos < 2) pos = 9;
        }

        const result = sum % 11 < 2 ? 0 : 11 - (sum % 11);
        return result === parseInt(cnpj.charAt(length));
    };

    return validateDigit(12) && validateDigit(13);
};

/**
 * Validação do form de Customer (client-side) para uso em Create/Edit.
 */
export const validateCustomerForm = (input: CustomerValidationInput): CustomerValidationErrors => {
    const errors: CustomerValidationErrors = {};

    if (!input.email || !isValidEmail(input.email)) {
        errors.email = 'Invalid email format.';
    }

    if (input.phone) {
        const phone = onlyNumbers(input.phone);

        if (phone.length < 10 || phone.length > 13) {
            errors.phone = 'Invalid phone number.';
        }
    }

    if (input.document) {
        const doc = onlyNumbers(input.document);

        if (!(isValidCPF(doc) || isValidCNPJ(doc))) {
            errors.document = 'Invalid CPF or CNPJ.';
        }
    }

    return errors;
};
