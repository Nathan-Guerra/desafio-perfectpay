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
}
