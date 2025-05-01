<?php

namespace App\Services;

use App\Exceptions\InvalidBillingTypeException;
use App\Exceptions\UnavailableBillingValidatorException;
use App\Http\Requests\StorePaymentRequest;

class PaymentService
{
    public function validatePaymentRequest(StorePaymentRequest $request): array
    {
        return (new PaymentValidatorService($request))->validate();
    }

}
