<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'payment_uuid' => (string)$this->payment_uuid,
            'type' => (string)$this->type,
            'value' => (int)$this->value,
            'currency' => (string)$this->currency,
            'status' => (string)$this->status,
            'due_at' => (string)$this->due_at,
        ];
    }
}
