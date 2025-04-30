<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    public function test_payment_endpoint_should_return_an_error_for_empty_body(): void
    {
        $response = $this->post(route('payment.store'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_payment_with_wrong_billing_type_should_return_an_error(): void
    {
        $payload = [
            'name' => 'João Silva',
            'cpfCnpj' => '12345678901',
            'billingType' => 'BOLET0',
            'value' => '15000',
        ];

        $response = $this->postJson('/payment', $payload);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonValidationErrorFor('billingType');
    }

    public function test_payment_with_a_negative_amount_should_return_an_error(): void
    {
        $payload = [
            'name' => 'Maria Souza',
            'cpfCnpj' => '98765432100',
            'billingType' => 'PIX',
            'value' => '-20000',
        ];

        $response = $this->postJson('/payment', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('value');
    }

    public function test_payment_with_invalid_credit_card_number_should_return_an_error(): void
    {
        $payload = [
            'name' => 'Carlos Lima',
            'cpfCnpj' => '11122233344',
            'billingType' => 'CREDIT_CARD',
            'value' => '25000',
            'creditCard' => [
                'holderName' => 'Carlos Lima',
                'number' => '5184019740373151',
                'expiryMonth' => '12',
                'expiryYear' => '2026',
                'ccv' => '123',
            ],
            'creditCardHolderInfo' => [
                'name' => 'Carlos Lima',
                'email' => 'carlos@example.com',
                'cpfCNPJ' => '11122233344',
                'postalCode' => '12345678',
                'addressNumber' => '99',
                'addressComplement' => 'Casa',
                'phone' => '1133557799',
            ],
        ];

        $response = $this->postJson('/payment', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('creditCard.number');
    }

    public function test_payment_with_boleto_should_redirect_to_the_payment_page(): void
    {
        $payload = [
            'Nome' => 'João Silva',
            'CPF' => '12345678901',
            'billingType' => 'BOLETO',
            'value' => '15000',
        ];

        $response = $this->postJson('/payment', $payload);

        $lastPaymment = Payment::latest('id');
        $response->assertRedirect(route('payment.show', ['paymentId' => $lastPaymment->payment_uuid]));
    }

    public function test_payment_with_pix_should_redirect_to_the_payment_page(): void
    {
        $payload = [
            'Nome' => 'Joao Silva',
            'CPF' => '99991111140',
            'billingType' => 'PIX',
            'value' => '20000',
        ];

        $response = $this->postJson('/payment', $payload);

        $lastPaymment = Payment::latest('id');
        $response->assertRedirect(route('payment.show', ['paymentId' => $lastPaymment->payment_uuid]));
    }

    public function test_payment_with_credit_card_should_redirect_to_the_payment_page(): void
    {
        $payload = [
            'name' => 'Carlos Lima',
            'cpfCnpj' => '11122233344',
            'billingType' => 'CREDIT_CARD',
            'value' => '25000',
            'creditCard' => [
                'holderName' => 'Carlos Lima',
                'number' => '444444444444',
                'expiryMonth' => '12',
                'expiryYear' => '2026',
                'ccv' => '123',
            ],
            'creditCardHolderInfo' => [
                'name' => 'Carlos Lima',
                'email' => 'carlos@example.com',
                'cpfCNPJ' => '11122233344',
                'postalCode' => '12345678',
                'addressNumber' => '99',
                'addressComplement' => 'Casa',
                'phone' => '1133557799',
            ],
        ];

        $response = $this->postJson('/payment', $payload);

        $lastPaymment = Payment::latest('id');
        $response->assertRedirect(route('payment.show', ['paymentId' => $lastPaymment->payment_uuid]));
    }

    public function test_payment_with_installments_credit_card_should_redirect_to_the_payment_page(): void
    {
        $payload = [
            'name' => 'Ana Paula',
            'cpfCnpj' => '55566677788',
            'billingType' => 'CREDIT_CARD',
            'value' => '50000',
            'installmentCount' => '3',
            'creditCard' => [
                'holderName' => 'Ana Paula',
                'number' => '4444444444444444',
                'expiryMonth' => '11',
                'expiryYear' => '2026',
                'ccv' => '321',
            ],
            'creditCardHolderInfo' => [
                'postalCode' => '87654321',
                'addressNumber' => '10A',
                'addressComplement' => 'Bloco B',
                'phone' => '1122334455',
            ],
        ];

        $response = $this->postJson('/payment', $payload);

        $lastPaymment = Payment::latest('id');
        $response->assertRedirect(route('payment.show', ['paymentId' => $lastPaymment->payment_uuid]));
    }
}
