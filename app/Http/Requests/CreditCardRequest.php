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
            'creditCard.expiryMonth' => 'required|digits:2|integer|min:1|max:12',
            'creditCard.expiryYear' => 'required|digits:4|integer|min:' . date('Y'),
            'creditCard.ccv' => 'required|digits:3',

            'creditCardHolderInfo.postalCode' => 'required|string|max:10',
            'creditCardHolderInfo.addressNumber' => 'required|string|max:10',
            'creditCardHolderInfo.addressComplement' => 'nullable|string|max:255',
        ]);
    }
}
