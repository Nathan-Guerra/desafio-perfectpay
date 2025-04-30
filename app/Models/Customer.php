<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'cpfCnpj',
        'phone',
    ];

    public function asaasCustomer(): HasOne
    {
        return $this->hasOne(AsaasCustomer::class);
    }

    public function asaasCreditCardToken(): HasOne
    {
        return $this->hasOne(AsaasCreditCardToken::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
