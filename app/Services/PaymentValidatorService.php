<?php

namespace App\Services;

use App\Enums\PaymentTypesEnum;
use App\Exceptions\InvalidBillingTypeException;
use App\Exceptions\UnavailableBillingValidatorException;
use App\Http\Requests\BoletoRequest;
use App\Http\Requests\CreditCardFullRequest;
use App\Http\Requests\CreditCardInstallmentRequest;
use App\Http\Requests\PixRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PaymentValidatorService
{
    private FormRequest $validator;

    public function __construct(
        private readonly Request $request,
    ) {
    }

    /**
     * @throws InvalidBillingTypeException
     * @throws UnavailableBillingValidatorException
     */
    public function validate(): array
    {
        $billingType = $this->request->input('billingType');

        if (!PaymentTypesEnum::isValidPaymentType($billingType)) {
            throw new InvalidBillingTypeException("Tipo de pagamento [$billingType] inválido.");
        }

        $billingTypeEnum = PaymentTypesEnum::from($billingType);
        if ($billingTypeEnum === PaymentTypesEnum::CREDIT_CARD) {
            if (!$this->request->input('installmentsCount')) {
                $billingTypeEnum = PaymentTypesEnum::CREDIT_CARD_FULL;
            } else {
                $billingTypeEnum = PaymentTypesEnum::CREDIT_CARD_INSTALLMENTS;
            }
        }

        $this->setValidator($billingTypeEnum);

        $this->validator->merge($this->request->all());
        $this->validator->validateResolved();

        return $this->validator->validated();
    }

    /**
     * @throws UnavailableBillingValidatorException
     */
    public function setValidator(PaymentTypesEnum $billingType): void
    {
        $this->validator = match ($billingType) {
            PaymentTypesEnum::BOLETO => app(BoletoRequest::class),
            PaymentTypesEnum::PIX => app(PixRequest::class),
            PaymentTypesEnum::CREDIT_CARD_FULL => app(CreditCardFullRequest::class),
            PaymentTypesEnum::CREDIT_CARD_INSTALLMENTS => app(CreditCardInstallmentRequest::class),
            default => throw new UnavailableBillingValidatorException("Sem validador para o tipo de pagamento [{$billingType->value}]."),
        };
    }
}
