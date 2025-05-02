<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentStandardFieldsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            app(StorePaymentRequest::class)->rules(),
            app(CustomerRequest::class)->rules(),
            [
                'value' => 'required|integer|gt:0',
            ],
        );
    }

    public function messages(): array
    {
        return [
            'value.required' => 'O campo :attribute é obrigatório.',
            'value.integer' => 'O campo :attribute deve ser um número inteiro.',
            'value.gt' => 'O campo :attribute deve ser maior que :value.',
        ];
    }

    public function attributes(): array
    {
        return [
            'value' => 'Valor',
        ];

    }
}
