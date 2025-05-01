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
                'dueDate' => 'required|date',
            ],
        );
    }
}
