<?php

namespace App\Services;

use App\Exceptions\InvalidBillingTypeException;
use App\Exceptions\UnmatchedBillingTypeException;
use Illuminate\Http\Request;

class PaymentService
{
    public function validatePaymentRequest(Request $request): array|false
    {
        try {
            return (new PaymentValidatorService($request))->validate();
        } catch (InvalidBillingTypeException|UnmatchedBillingTypeException) {
            return false;
        }
    }

}
