<?php

namespace App\Services;

use App\Enums\PaymentTypesEnum;
use App\Exceptions\AsaasConfigurationException;
use App\Exceptions\AsaasRequestException;
use App\Models\AsaasCreditCardToken;
use App\Models\AsaasCustomer;
use App\Models\Customer;
use App\Models\Payment;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Ramsey\Uuid\Uuid;

class AsaasService
{
    private const string VERSION = 'v3';

    private string $url;
    private string $apiKey;

    private Client $client;

    public function __construct()
    {
        $this->apiKey = config('paymentGateways.asaas.api_key');
        $this->client = app('insecure-http-client');

        $this->mountUrl();
    }

    /**
     * @throws AsaasConfigurationException
     * @throws GuzzleException
     * @throws AsaasRequestException
     */
    public function requestAsaas(string $path, array $body, string $method = 'POST', array $options = []): array
    {
        $endpoint = $this->makeEndpoint($path);
        $response = $this->client->request(
            $method,
            $endpoint,
            array_merge($options, [
                'body' => json_encode($body),
                'headers' => $this->getHeaders(),
            ])
        );

        $responseArray = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() === 400 && is_array($responseArray['errors'])) {
            throw new AsaasRequestException(current($responseArray['errors'])['description']);
        }

        if ($response->getStatusCode() === 401) {
            throw new AsaasConfigurationException("Sem acesso ao Asaas. Utilizando a chave [{$this->apiKey}]");
        }

        if ($response->getStatusCode() === 404) {
            throw new AsaasConfigurationException("Endpoint [$endpoint] não encontrado.");
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            var_dump('Response code: ', $response->getStatusCode());
            var_dump('Response body: ', $response->getBody()->getContents());
            var_dump('JSON Error: ', json_last_error_msg());

            die();
        }

        return $responseArray;
    }

    /**
     * @throws AsaasConfigurationException
     * @throws GuzzleException
     * @throws AsaasRequestException
     */
    public function createCustomer(Customer $customer): array
    {
        return $this->requestAsaas('customers', [
            'name' => $customer->name,
            'cpfCnpj' => $customer->cpfCnpj,
        ]);
    }

    /**
     * @throws AsaasConfigurationException
     * @throws GuzzleException
     * @throws AsaasRequestException
     */
    public function requestBoleto(Customer $customer, array $payload): array
    {
        return $this->requestAsaas('payments', [
            'customer' => $customer->asaasCustomer->asaas_id,
            'billingType' => $payload['billingType'],
            'value' => $payload['value'] / 100,
            'dueDate' => $this->dueDateByPaymentType($payload['billingType']),
        ]);
    }

    /**
     * @throws AsaasConfigurationException
     * @throws GuzzleException
     * @throws AsaasRequestException
     */
    public function requestPix(Customer $customer, array $payload): array
    {
        return $this->requestAsaas('payments', [
            'customer' => $customer->asaasCustomer->asaas_id,
            'billingType' => $payload['billingType'],
            'value' => $payload['value'] / 100,
            'dueDate' => $this->dueDateByPaymentType($payload['billingType']),
        ]);
    }

    /**
     * @throws AsaasConfigurationException
     * @throws GuzzleException
     * @throws AsaasRequestException
     */
    public function requestCreditCard(Customer $customer, array $payload): array
    {
        return $this->requestAsaas('payments', [
            'customer' => $customer->asaasCustomer->asaas_id,
            'billingType' => $payload['billingType'],
            'value' => $payload['value'] / 100,
            'dueDate' => $this->dueDateByPaymentType($payload['billingType']),
            'creditCardToken' => $customer->asaasCreditCardToken->token,
        ]);
    }

    /**
     * @throws AsaasConfigurationException
     * @throws GuzzleException
     * @throws AsaasRequestException
     */
    public function tokenizacaoCartaoCredito(Customer $customer, array $payload): array
    {
        return $this->requestAsaas(
            'creditCard/tokenizeCreditCard',
            [
                'customer' => $customer->asaasCustomer->asaas_id,
                'creditCard' => [
                    'holderName' => $customer->name,
                    'number' => $payload['creditCard']['number'],
                    'expiryMonth' => $payload['creditCard']['expiryMonth'],
                    'expiryYear' => $payload['creditCard']['expiryYear'],
                    'ccv' => $payload['creditCard']['ccv'],
                ],
                'creditCardHolderInfo' => [
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'cpfCnpj' => $customer->cpfCnpj,
                    'postalCode' => $payload['creditCardHolderInfo']['postalCode'],
                    'addressNumber' => $payload['creditCardHolderInfo']['addressNumber'],
                    'addressComplement' => $payload['creditCardHolderInfo']['addressComplement'] ?? null,
                    'phone' => $customer->phone,
                ],
            ],
            options: ['remoteIp' => $payload['ip'],]
        );
    }

    public function linhaDigitavelBoleto(Payment $payment)
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->makeEndpoint(sprintf('payments/%s/identificationField', $payment->external_reference)),
                ['headers' => $this->getHeaders()],
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            die('GuzzleException at ' . __FILE__ . ': ' . $e->getMessage());
        }
    }

    public function qrCodePix(Payment $payment)
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->makeEndpoint(sprintf('payments/%s/pixQrCode', $payment->external_reference)),
                ['headers' => $this->getHeaders()],
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            die('GuzzleException at ' . __FILE__ . ': ' . $e->getMessage());
        }
    }

    private function mountUrl(): void
    {
        $sanitizedUrl = rtrim(config('paymentGateways.asaas.url'), '/');
        $this->url = sprintf('%s/%s', $sanitizedUrl, self::VERSION);
    }

    private function makeEndpoint(string $endpoint): string
    {
        return sprintf('%s/%s', $this->url, $endpoint);
    }

    private function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Access_token' => $this->apiKey,
        ];
    }

    /**
     * @throws AsaasRequestException
     * @throws GuzzleException
     * @throws AsaasConfigurationException
     * @throws \Exception
     */
    public function pay(array $payload)
    {
        $customer = $this->getCustomer([
            'name' => $payload['name'],
            'cpfCnpj' => $payload['cpfCnpj'],
            'email' => $payload['email'],
            'phone' => $payload['phone'],
        ]);

        $duplicated = $this->validateDuplicity($customer, $payload);
        if ($duplicated) {
            return $duplicated;
        }

        $billingType = $payload['billingType'];

        if ($billingType === PaymentTypesEnum::BOLETO->value) {
            $paymentResponse = $this->requestBoleto($customer, $payload);
        } elseif ($billingType === PaymentTypesEnum::PIX->value) {
            $paymentResponse = $this->requestPix($customer, $payload);
        } else {
            $customer = $this->loadCreditCard($customer, $payload);
            $paymentResponse = $this->requestCreditCard($customer, $payload);
        }

        return $this->createPayment($customer, $paymentResponse);
    }

    /**
     * @throws AsaasConfigurationException
     * @throws GuzzleException
     * @throws AsaasRequestException
     */
    private function getCustomer(array $array): Customer
    {
        $customer = Customer::query()
            ->with('asaasCustomer')
            ->where('cpfCnpj', $array['cpfCnpj'])
            ->first();

        if (!$customer) {
            $customer = (new Customer())->fill($array);
            $userSaved = $customer->save();
            if (!$userSaved) {
                throw new \Exception('Erro ao salvar o cliente');
            }

            $asaasCustomerResponse = $this->createCustomer($customer);
            $asaasCustomer = (new AsaasCustomer())->fill([
                'asaas_id' => $asaasCustomerResponse['id'],
            ]);
            $asaasCustomer->customer()->associate($customer);

            $customer->setRelation('asaasCustomer', $asaasCustomer);
            $asaasCustomer->save();
        }

        return $customer;
    }

    private function validateDuplicity(Customer $customer, array $payload)
    {
        return Payment::query()
            ->where('customer_id', $customer->id)
            ->where('type', $payload['billingType'])
            ->where('value', $payload['value'])
            ->where('currency', 'BRL')
            ->where('due_at', $this->dueDateByPaymentType($payload['billingType']))
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();
    }

    /**
     * @throws AsaasConfigurationException
     * @throws GuzzleException
     * @throws AsaasRequestException
     */
    private function loadCreditCard(Customer $customer, array $payload): Customer
    {
        $asaasCreditCardToken = AsaasCreditCardToken::query()
            ->where('customer_id', $customer->id)
            ->where('number', substr($payload['creditCard']['number'], -4))
            ->first();

        if (!$asaasCreditCardToken) {
            $creditCardToken = $this->tokenizacaoCartaoCredito($customer, $payload);
            $asaasCreditCardToken = (new AsaasCreditCardToken())->fill([
                'token' => $creditCardToken['creditCardToken'],
                'brand' => $creditCardToken['creditCardBrand'],
                'number' => $creditCardToken['creditCardNumber'],
            ]);

            $asaasCreditCardToken->customer()->associate($customer);
            $asaasCreditCardToken->save();
        }

        $customer->setRelation('asaasCreditCardToken', $asaasCreditCardToken);

        return $customer;
    }

    public function dueDateByPaymentType(string $enum): string
    {
        $now = now();
        return match ($enum) {
            PaymentTypesEnum::BOLETO->value => $now->add(new \DateInterval('P7D'))->format('Y-m-d'),
            default => $now->add(new \DateInterval('PT30M'))->format('Y-m-d'),
        };
    }

    public function createPayment(Customer $customer, array $paymentResponse): Payment
    {
        $payment = new Payment();
        $payment->customer()->associate($customer);
        $payment->gateway_id = 1;
        $payment->payment_uuid = Uuid::uuid4()->toString();
        $payment->external_reference = $paymentResponse['id'];
        $payment->type = $paymentResponse['billingType'];
        $payment->value = $paymentResponse['value'];
        $payment->currency = 'BRL';
        $payment->status = $paymentResponse['status'];
        $payment->due_at = $paymentResponse['dueDate'];
        $payment->save();

        return $payment;
    }
}
