<?php

namespace App\Enums;

enum PaymentTypesEnum: string
{
    case BOLETO = 'BOLETO';
    case PIX = 'PIX';
    case CREDIT_CARD = 'CREDIT_CARD';

    public function label(): string
    {
        return match ($this) {
            self::BOLETO => 'Boleto',
            self::PIX => 'Pix',
            self::CREDIT_CARD => 'Cartão de Crédito',
        };
    }
}
