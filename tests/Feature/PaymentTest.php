<?php

namespace Tests\Feature;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * @var class-string<DatabaseSeeder>
     */
    protected string $seeder = DatabaseSeeder::class;

    public function test_payment_endpoint_should_return_an_error_for_empty_body(): void
    {
        $response = $this->postJson(route('api.payments.store'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_payment_with_wrong_billing_type_should_return_an_error(): void
    {
        $payload = [
            'name' => 'João Silva',
            'email' => 'joao.silva@teste.com',
            'phone' => '24999084601',
            'cpfCnpj' => '12345678901',
            'billingType' => 'BOLET0',
            'value' => '15000',
        ];

        $response = $this->postJson(route('api.payments.store'), $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('billingType');
    }

    public function test_payment_with_a_negative_amount_should_return_an_error(): void
    {
        $payload = [
            'name' => 'Maria Souza',
            'email' => 'maria.souza@teste.com',
            'phone' => '24999084601',
            'cpfCnpj' => '98765432100',
            'billingType' => 'PIX',
            'value' => '-20000',
        ];

        $response = $this->postJson(route('api.payments.store'), $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('value');
    }

    public function test_payment_with_invalid_credit_card_number_should_return_an_error(): void
    {
        $payload = [
            'name' => 'Carlos Lima',
            'email' => 'carlos.lima@teste.com',
            'phone' => '24999084601',
            'cpfCnpj' => '11122233344',
            'billingType' => 'CREDIT_CARD',
            'value' => '25000',
            'creditCard' => [
                'number' => '5184019740373151',
                'expiryMonth' => '12',
                'expiryYear' => '2026',
                'ccv' => '123',
            ],
            'creditCardHolderInfo' => [
                'postalCode' => '12345678',
                'addressNumber' => '99',
                'addressComplement' => 'Casa',
                'phone' => '1133557799',
            ],
        ];

        $response = $this->postJson(route('api.payments.store'), $payload);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_payment_with_correct_boleto_payload_should_succeed(): void
    {
        $payload = [
            'name' => 'João Silva',
            'email' => 'joao.silva@teste.com',
            'phone' => '24999084601',
            'cpfCnpj' => '07894953091',
            'billingType' => 'BOLETO',
            'value' => '15000',
        ];

        $response = $this->postJson(route('api.payments.store'), $payload);
        $lastPaymment = Payment::query()->latest('id')->first();

        $this->assertSame((new PaymentResource($lastPaymment))->toArray(\request()), $response['data']);

        $response->assertStatus(201);
    }

    public function test_payment_with_pix_should_redirect_to_the_payment_page(): void
    {
        $payload = [
            'name' => 'Joao Silva',
            'email' => 'joao.silva@teste.com',
            'phone' => '24999084601',
            'cpfCnpj' => '07894953091',
            'billingType' => 'PIX',
            'value' => '20000',
        ];

        $response = $this->postJson(route('api.payments.store'), $payload);
        $lastPaymment = Payment::query()->latest('id')->first();

        $this->assertSame((new PaymentResource($lastPaymment))->toArray(\request()), $response['data']);

        $response->assertStatus(201);
    }

    public function test_payment_with_credit_card_should_redirect_to_the_payment_page(): void
    {
        $payload = [
            'name' => 'Carlos Lima',
            'email' => 'carlos.lima@teste.com',
            'phone' => '24999084601',
            'cpfCnpj' => '07894953091',
            'billingType' => 'CREDIT_CARD',
            'value' => '25000',
            'creditCard' => [
                'number' => '4444333322221111',
                'expiryMonth' => '12',
                'expiryYear' => '2026',
                'ccv' => '123',
            ],
            'creditCardHolderInfo' => [
                'postalCode' => '27522200',
                'addressNumber' => '99',
                'addressComplement' => 'Casa',
            ],
        ];

        $response = $this->postJson(route('api.payments.store'), $payload);
        $lastPaymment = Payment::query()->latest('id')->first();

        $response->dump();

        $this->assertSame(new PaymentResource($lastPaymment)->toArray(\request()), $response['data']);

        $response->assertStatus(201);
    }
}
