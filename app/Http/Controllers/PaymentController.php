<?php

namespace App\Http\Controllers;

use App\Http\Requests\BoletoRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request, PaymentService $paymentService)
    {
        $validated = $paymentService->validatePaymentRequest($request);

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
