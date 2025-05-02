<?php

namespace App\Http\Requests;

use App\Enums\PaymentTypesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'billingType' => [
                'required',
                Rule::enum(PaymentTypesEnum::class),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'billingType.required' => 'O campo :attribute é obrigatório.',
            'billingType.enum' => 'O campo :attribute deve ser um valor válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'billingType' => 'Tipo de pagamento',
        ];
    }
}
