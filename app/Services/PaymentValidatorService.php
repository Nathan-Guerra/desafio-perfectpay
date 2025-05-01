<?php

namespace App\Services;

use App\Enums\PaymentTypesEnum;
use App\Http\Requests\BoletoRequest;
use App\Http\Requests\CreditCardRequest;
use App\Http\Requests\PixRequest;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Foundation\Http\FormRequest;

class PaymentValidatorService
{
    private FormRequest $validator;

    public function __construct(
        private readonly StorePaymentRequest $request,
    ) {
    }

    public function validate(): array
    {
        $billingType = PaymentTypesEnum::from($this->request->input('billingType'));

        $this->setValidator($billingType);

        $this->validator->merge($this->request->all());
        $this->validator->validateResolved();

        return $this->validator->validated();
    }

    public function setValidator(PaymentTypesEnum $billingType): void
    {
        $this->validator = match ($billingType) {
            PaymentTypesEnum::BOLETO => app(BoletoRequest::class),
            PaymentTypesEnum::PIX => app(PixRequest::class),
            PaymentTypesEnum::CREDIT_CARD => app(CreditCardRequest::class),
        };
    }
}
