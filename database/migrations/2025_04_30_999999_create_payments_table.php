<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('gateway_id')->constrained('gateways');
            $table->uuid('payment_uuid')->unique();
            $table->string('external_reference');
            $table->string('type');
            $table->unsignedInteger('value');
            $table->string('currency', 3);
            $table->string('status');
            $table->date('due_at');
            $table->timestamps();
        });
    }
};
