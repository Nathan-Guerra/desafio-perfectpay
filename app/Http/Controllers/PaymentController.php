<?php

namespace App\Http\Controllers;

use App\Enums\PaymentTypesEnum;
use App\Exceptions\AsaasRequestException;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\AsaasService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(StorePaymentRequest $request, PaymentService $paymentService): JsonResponse
    {
        $validated = $paymentService->validatePaymentRequest($request);
        if ($validated['error'] ?? false) {
            return response()->json(
                array_filter($validated, fn ($k) => $k != 'error', ARRAY_FILTER_USE_KEY),
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $validated['ip'] = $request->ip();

            DB::beginTransaction();
            /** @var AsaasService $asaasService */
            $asaasService = app(AsaasService::class);
            $payment = $asaasService->pay($validated);
            DB::commit();
        } catch (AsaasRequestException $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao processar o pagamento.',
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao processar o pagamento.',
                'error' => 'Erro ao processar o pagamento.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Pagamento processado corretamente.',
            'data' => new PaymentResource($payment),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $paymentUuid)
    {
        $billingType = PaymentTypesEnum::from($paymentUuid->type);
        $asaasService = app(AsaasService::class);

        $payload = [
            'paymentType' => $billingType->value,
        ];

        if ($billingType === PaymentTypesEnum::BOLETO) {
            $boletoData = $asaasService->linhaDigitavelBoleto($paymentUuid);
            $payload['boletoCode'] = $boletoData['identificationField'];
        }

        if ($billingType === PaymentTypesEnum::PIX) {
            $pixData = $asaasService->qrCodePix($paymentUuid);
            $payload['pixImage'] = $pixData['encodedImage'];
            $payload['pixCode'] = $pixData['payload'];
            $payload['dueDate'] = (new \DateTime($pixData['expirationDate']))->format('d/m/Y H:i:s');
        }

        if ($billingType === PaymentTypesEnum::CREDIT_CARD) {
            $payload['status'] = $paymentUuid->status;
        }
        return view('confirmation', $payload);
    }
}
