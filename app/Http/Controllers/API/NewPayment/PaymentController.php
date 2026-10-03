<?php

namespace App\Http\Controllers\API\NewPayment;

use App\Http\Controllers\Controller;
use App\Services\NewPayment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * PaymentController constructor.
     *
     * PHP 7.4 compatible.
     */
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Read student payment information.
     *
     * POST /api/new-payment/read
     */
    public function read(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        try {
            $data = $this->paymentService->read(
                $validated['code']
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Student payment information loaded successfully.',
                'data' => $data,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
        } catch (\Throwable $e) {
            Log::error(
                'New payment read failed.',
                [
                    'code' => isset($validated['code'])
                        ? $validated['code']
                        : null,

                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to load student payment information.',
            ], 500);
        }
    }

    /**
     * Store one payment.
     *
     * POST /api/new-payment/pay
     */
    public function pay(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
            ],

            'enrollment_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'payment_month' => [
                'required',
                'date',
            ],

            'mark_method' => [
                'required',
                'in:qr_mobile,qr_web,manual_mobile,manual_web',
            ],

            'payment_method' => [
                'nullable',
                'in:cash,card,bank_transfer,online,cheque,other',
            ],

            'discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'reference_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'note' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            $data = $this->paymentService->pay(
                $validated
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Payment completed successfully.',
                'data' => $data,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (\RuntimeException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 409);
        } catch (\Throwable $e) {
            Log::error(
                'New payment failed.',
                [
                    'code' => isset($validated['code'])
                        ? $validated['code']
                        : null,

                    'enrollment_id' => isset(
                        $validated['enrollment_id']
                    )
                        ? $validated['enrollment_id']
                        : null,

                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Payment could not be completed.',
            ], 500);
        }
    }

    /**
     * Store multiple payments.
     *
     * POST /api/new-payment/bulk-pay
     */
    public function bulkPay(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payments' => [
                'required',
                'array',
                'min:1',
            ],

            'payments.*.code' => [
                'required',
                'string',
                'max:100',
            ],

            'payments.*.enrollment_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'payments.*.amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'payments.*.payment_month' => [
                'required',
                'date',
            ],

            'payments.*.mark_method' => [
                'required',
                'in:qr_mobile,qr_web,manual_mobile,manual_web',
            ],

            'payments.*.payment_method' => [
                'nullable',
                'in:cash,card,bank_transfer,online,cheque,other',
            ],

            'payments.*.discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'payments.*.reference_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'payments.*.note' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            $data = $this->paymentService->bulkPay(
                $validated['payments']
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Bulk payment completed successfully.',
                'data' => $data,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (\RuntimeException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 409);
        } catch (\Throwable $e) {
            Log::error(
                'New bulk payment failed.',
                [
                    'payment_count' => isset(
                        $validated['payments']
                    )
                        ? count($validated['payments'])
                        : 0,

                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Bulk payment could not be completed.',
            ], 500);
        }
    }
}