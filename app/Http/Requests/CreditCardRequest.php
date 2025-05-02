<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditCardRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(app(PaymentStandardFieldsRequest::class)->rules(), [
            'creditCard.number' => 'required|string',
            'creditCard.expiryMonth' => 'required|integer|min:1|max:12',
            'creditCard.expiryYear' => 'required|digits:4|integer|min:' . date('Y'),
            'creditCard.ccv' => 'required|digits:3',

            'creditCardHolderInfo.postalCode' => 'required|string|max:10',
            'creditCardHolderInfo.addressNumber' => 'required|string|max:10',
            'creditCardHolderInfo.addressComplement' => 'nullable|string|max:255',
        ]);
    }

    // criar o metodo messages e o metodo attributes para as regras acima
    public function messages(): array
    {
        return [
            'creditCard.number.required' => 'O campo :attribute é obrigatório.',
            'creditCard.number.string' => 'O campo :attribute deve ser uma string.',
            'creditCard.expiryMonth.required' => 'O campo :attribute é obrigatório.',
            'creditCard.expiryMonth.integer' => 'O campo :attribute deve ser um número inteiro.',
            'creditCard.expiryMonth.min' => 'O campo :attribute deve ser maior ou igual a :value.',
            'creditCard.expiryMonth.max' => 'O campo :attribute deve ser menor ou igual a :value.',
            'creditCard.expiryYear.required' => 'O campo :attribute é obrigatório.',
            'creditCard.expiryYear.digits' => 'O campo :attribute deve ter :digits dígitos.',
            'creditCard.expiryYear.integer' => 'O campo :attribute deve ser um número inteiro.',
            'creditCard.expiryYear.min' => 'O campo :attribute deve ser maior ou igual a :value.',
            'creditCard.ccv.required' => 'O campo :attribute é obrigatório.',
            'creditCard.ccv.digits' => 'O campo :attribute deve ter :digits dígitos.',

            'creditCardHolderInfo.postalCode.required' => 'O campo :attribute é obrigatório.',
            'creditCardHolderInfo.postalCode.string' => 'O campo :attribute deve ser uma string.',
            'creditCardHolderInfo.postalCode.max' => 'O campo :attribute deve ter no máximo :value caracteres.',

            'creditCardHolderInfo.addressNumber.required' => 'O campo :attribute é obrigatório.',
            'creditCardHolderInfo.addressNumber.string' => 'O campo :attribute deve ser uma string.',
            'creditCardHolderInfo.addressNumber.max' => 'O campo :attribute deve ter no máximo :value caracteres.',

            'creditCardHolderInfo.addressComplement.string' => 'O campo :attribute deve ser uma string.',
            'creditCardHolderInfo.addressComplement.max' => 'O campo :attribute deve ter no máximo :value caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'creditCard.number' => 'Número do cartão',
            'creditCard.expiryMonth' => 'Mês de validade',
            'creditCard.expiryYear' => 'Ano de validade',
            'creditCard.ccv' => 'CVC',

            'creditCardHolderInfo.postalCode' => 'CEP',
            'creditCardHolderInfo.addressNumber' => 'Número do endereço',
            'creditCardHolderInfo.addressComplement' => 'Complemento do endereço',
        ];
    }
}
