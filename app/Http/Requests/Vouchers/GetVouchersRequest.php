<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class GetVouchersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['required', 'int', 'gt:0'],
            'paginate' => ['required', 'int', 'gt:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'series' => ['nullable', 'string'],
            'number' => ['nullable', 'string'],
            'voucher_type' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'in:PEN,USD'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'El campo start_date es obligatorio.',
            'end_date.required' => 'El campo end_date es obligatorio.',
            'end_date.after_or_equal' => 'La fecha end_date debe ser igual o posterior a start_date.',
        ];
    }
}
