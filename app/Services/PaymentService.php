<?php

namespace App\Services;

use App\Http\Requests\StorePaymentRequest;

class PaymentService
{
    public function validatePaymentRequest(StorePaymentRequest $request): array
    {
        return (new PaymentValidatorService($request))->validate();
    }
}
