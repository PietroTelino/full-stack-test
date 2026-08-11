<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lib\Tenancy\Tenant;

class UpdateInvoiceRequest extends FormRequest
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
        $invoice = $this->route('invoice');

        $issueDateForComparison = $this->input('issue_date')
            ? 'issue_date'
            : ($invoice?->issue_date?->toDateString() ?? 'today');

        $customerExists = Rule::exists('customers', 'id');
        if ($tenantId && $tenantId !== '0') {
            $customerExists->where('team_id', $tenantId);
        }

        $invoiceItemExists = Rule::exists('invoice_items', 'id');
        if ($tenantId && $tenantId !== '0') {
            $invoiceItemExists->where('team_id', $tenantId);
        }

        return [
            'customer_id' => ['sometimes', 'required', $customerExists],
            'status' => ['sometimes', 'required', 'string', 'in:draft,pending,issued,paid,overdue,cancelled'],
            'issue_date' => ['sometimes', 'required', 'date'],
            'due_date' => ['sometimes', 'required', 'date', 'after_or_equal:'.$issueDateForComparison],
            'payment_date' => ['nullable', 'date', 'after_or_equal:'.$issueDateForComparison],
            'metadata' => ['nullable', 'array'],
            'items' => ['sometimes', 'required', 'array', 'min:1'],
            'items.*.id' => ['nullable', $invoiceItemExists],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.subtitle' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'integer', 'min:0'],
        ];
    }
}
