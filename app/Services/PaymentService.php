<?php

namespace App\Services;

use App\Exceptions\InvalidBillingTypeException;
use App\Exceptions\UnavailableBillingValidatorException;
use Illuminate\Http\Request;

class PaymentService
{
    public function validatePaymentRequest(Request $request): array
    {
        try {
            return (new PaymentValidatorService($request))->validate();
        } catch (InvalidBillingTypeException|UnavailableBillingValidatorException $e) {
            return [
                'error' => true,
                'message' => 'Erro ao processar o pagamento.',
                'errors' => [
                    'billingType' => $e->getMessage()
                ]
            ];
        }
    }

}
