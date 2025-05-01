<?php

namespace App\Http\Controllers;

use App\Http\Requests\BoletoRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function store(StorePaymentRequest $request, PaymentService $paymentService): JsonResponse
    {
        $validated = $paymentService->validatePaymentRequest($request);
        if ($validated['error'] ?? false) {
            return response()->json(
                array_filter($validated, fn($k) => $k != 'error', ARRAY_FILTER_USE_KEY),
                Response::HTTP_BAD_REQUEST
            );
        }

        // Proceed with payment processing if validation passes
        return response()->json([
            'message' => 'Payment processed successfully',
            'data' => $validated
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $paymentUuid)
    {
        //
    }
}
