<?php

namespace App\Enums;

enum PaymentTypesEnum: string
{
    case BOLETO = 'BOLETO';
    case PIX = 'PIX';
    case CREDIT_CARD = 'CREDIT_CARD';

    case CREDIT_CARD_FULL = 'CREDIT_CARD_FULL';
    case CREDIT_CARD_INSTALLMENTS = 'CREDIT_CARD_INSTALLMENTS';

    public static function isValidPaymentType(string $type): bool
    {
        $enum = self::tryFrom($type);

        return in_array($enum, [self::BOLETO, self::PIX, self::CREDIT_CARD], true);
    }

    public function label(): string
    {
        return match ($this) {
            self::BOLETO => 'Boleto',
            self::PIX => 'Pix',
            self::CREDIT_CARD => 'Cartão de Crédito',
            self::CREDIT_CARD_FULL => 'Cartão de Crédito à Vista',
            self::CREDIT_CARD_INSTALLMENTS => 'Cartão de Crédito Parcelado',
        };
    }
}
