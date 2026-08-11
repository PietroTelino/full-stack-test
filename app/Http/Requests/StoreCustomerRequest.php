<?php

namespace App\Http\Requests;

use App\Rules\ValidCpfOrCnpj;
use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lib\Tenancy\Tenant;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tenantId = Tenant::current()?->id();

        $emailUnique = Rule::unique('customers', 'email');
        if ($tenantId && $tenantId !== '0') {
            $emailUnique->where('team_id', $tenantId);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailUnique],
            'phone' => ['nullable', 'string', 'max:50', new ValidPhoneNumber()],
            'address' => ['nullable', 'string', 'max:500'],
            'document' => ['nullable', 'string', 'max:100', new ValidCpfOrCnpj()],
        ];
    }
}
