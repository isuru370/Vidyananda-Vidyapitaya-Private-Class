<?php

namespace App\Services\NewPayment;

use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentClassEnrollment;
use App\Services\ReceiptNumberService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

class PaymentMarker
{
    /**
     * Allowed payment marking methods.
     */
    private const ALLOWED_MARK_METHODS = [
        'qr_mobile',
        'qr_web',
        'manual_mobile',
        'manual_web',
    ];

    /**
     * Allowed payment methods.
     */
    private const ALLOWED_PAYMENT_METHODS = [
        'cash',
        'card',
        'bank_transfer',
        'online',
        'cheque',
        'other',
    ];

    /**
     * Create one completed payment.
     *
     * Payment rules:
     *
     * 1. Student must own the enrollment.
     * 2. Payment month is required.
     * 3. Any month can be selected.
     * 4. Same enrollment + same month cannot be paid twice.
     * 5. Payable amount =
     *      Class Fee + Hall Fee - Discount
     * 6. Payment amount must cover payable amount.
     * 7. Every payment gets its own receipt number.
     */
    public function mark(
        Student $student,
        StudentClassEnrollment $enrollment,
        array $data
    ): Payment {
        /*
        |--------------------------------------------------------------------------
        | 1. Verify Enrollment Belongs To Student
        |--------------------------------------------------------------------------
        */

        if ((int) $enrollment->student_id !== (int) $student->id) {
            throw new \RuntimeException(
                'The selected class enrollment does not belong to this student.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Mark Method
        |--------------------------------------------------------------------------
        */

        $markMethod = isset($data['mark_method'])
            ? $data['mark_method']
            : null;

        if (!in_array(
            $markMethod,
            self::ALLOWED_MARK_METHODS,
            true
        )) {
            throw new \InvalidArgumentException(
                'Invalid payment mark method.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Payment Method
        |--------------------------------------------------------------------------
        */

        $paymentMethod = isset($data['payment_method'])
            ? $data['payment_method']
            : 'cash';

        if (!in_array(
            $paymentMethod,
            self::ALLOWED_PAYMENT_METHODS,
            true
        )) {
            throw new \InvalidArgumentException(
                'Invalid payment method.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Payment Month
        |--------------------------------------------------------------------------
        |
        | User can select ANY month.
        |
        | Example:
        | August 2026
        | September 2026
        | October 2026
        |
        */

        if (
            !isset($data['payment_month']) ||
            trim((string) $data['payment_month']) === ''
        ) {
            throw new \InvalidArgumentException(
                'Payment month is required.'
            );
        }

        try {
            $paymentMonth = Carbon::parse(
                $data['payment_month']
            )->startOfMonth();
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException(
                'Invalid payment month.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Payment Amount
        |--------------------------------------------------------------------------
        */

        $amount = isset($data['amount'])
            ? (float) $data['amount']
            : 0;

        if ($amount <= 0) {
            throw new \InvalidArgumentException(
                'Payment amount must be greater than zero.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Discount Amount
        |--------------------------------------------------------------------------
        */

        $discountAmount = isset($data['discount_amount'])
            ? (float) $data['discount_amount']
            : 0;

        if ($discountAmount < 0) {
            throw new \InvalidArgumentException(
                'Discount amount cannot be negative.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Get Class Fee
        |--------------------------------------------------------------------------
        |
        | The enrollment now gets its fee from:
        |
        | StudentClassEnrollment
        |       ↓
        | ClassCategoryFee
        |       ↓
        | ClassCategoryFeeOption
        |
        | final_fee already handles:
        |
        | - selected fee option
        | - free card
        |
        */

        $classFee = (float) $enrollment->final_fee;

        if ($classFee < 0) {
            throw new \InvalidArgumentException(
                'Invalid enrollment fee.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Get Hall Fee
        |--------------------------------------------------------------------------
        |
        | Hall fee belongs to the active schedule pattern.
        |
        | We select the schedule pattern belonging to the same
        | class category fee.
        |
        */

        $hallFee = 0;

        $studentClass = $enrollment->studentClass;

        if ($studentClass) {
            $pattern = $studentClass
                ->schedulePatterns()
                ->where('is_active', true)
                ->where(
                    'class_category_fee_id',
                    $enrollment->class_category_fee_id
                )
                ->with('hall')
                ->latest('start_date')
                ->first();

            if ($pattern && $pattern->hall) {
                $hallFee = (float) $pattern->hall->hall_price;
            }
        }

        if ($hallFee < 0) {
            throw new \InvalidArgumentException(
                'Invalid hall fee.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Calculate Gross Fee
        |--------------------------------------------------------------------------
        |
        | Class Fee + Hall Fee
        |
        */

        $grossFee = round(
            $classFee + $hallFee,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | 10. Validate Discount
        |--------------------------------------------------------------------------
        */

        if ($discountAmount > $grossFee) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Discount amount cannot exceed the total fee of Rs. %.2f.',
                    $grossFee
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 11. Calculate Payable Amount
        |--------------------------------------------------------------------------
        |
        | Gross Fee
        |      -
        | Discount
        |      =
        | Payable
        |
        */

        $payableAmount = round(
            max(
                $grossFee - $discountAmount,
                0
            ),
            2
        );

        /*
        |--------------------------------------------------------------------------
        | 12. Validate Payment Amount
        |--------------------------------------------------------------------------
        */

        if ($amount < $payableAmount) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Payment amount must be at least Rs. %.2f.',
                    $payableAmount
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 13. Duplicate Payment Check
        |--------------------------------------------------------------------------
        |
        | Same enrollment + same month cannot be paid twice.
        |
        */

        $alreadyPaid = Payment::query()
            ->where(
                'student_class_enrollment_id',
                $enrollment->id
            )
            ->whereDate(
                'payment_month',
                $paymentMonth
            )
            ->where(
                'status',
                'completed'
            )
            ->exists();

        if ($alreadyPaid) {
            throw new \RuntimeException(
                'Payment already completed for this class and month.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 14. Paid At
        |--------------------------------------------------------------------------
        */

        $paidAt = isset($data['paid_at'])
            ? $data['paid_at']
            : now();

        if (!$paidAt instanceof Carbon) {
            try {
                $paidAt = Carbon::parse($paidAt);
            } catch (\Throwable $e) {
                throw new \InvalidArgumentException(
                    'Invalid payment date/time.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 15. Generate Unique Receipt Number
        |--------------------------------------------------------------------------
        */

        $receiptNumber = ReceiptNumberService::generate();

        /*
        |--------------------------------------------------------------------------
        | 16. Create Payment
        |--------------------------------------------------------------------------
        */

        try {
            return Payment::create([
                'student_id' => $student->id,

                'student_class_enrollment_id' => $enrollment->id,

                'user_id' => auth()->id(),

                'mark_method' => $markMethod,

                'amount' => round($amount, 2),

                'discount_amount' => round(
                    $discountAmount,
                    2
                ),

                'paid_at' => $paidAt,

                'payment_month' => $paymentMonth,

                'payment_method' => $paymentMethod,

                'status' => 'completed',

                'receipt_number' => $receiptNumber,

                'reference_number' => isset(
                    $data['reference_number']
                )
                    ? $data['reference_number']
                    : null,

                'is_synced' => true,

                'note' => isset($data['note'])
                    ? $data['note']
                    : null,
            ]);
        } catch (QueryException $e) {
            /*
            |--------------------------------------------------------------------------
            | 17. Database-Level Duplicate Protection
            |--------------------------------------------------------------------------
            |
            | The UNIQUE constraint:
            |
            | student_class_enrollment_id
            | +
            | payment_month
            |
            | protects against two devices creating the same
            | payment at exactly the same time.
            |
            */

            $message = strtolower(
                $e->getMessage()
            );

            if (
                strpos(
                    $message,
                    'unique_enrollment_month_payment'
                ) !== false
            ) {
                throw new \RuntimeException(
                    'Payment already completed for this class and month.'
                );
            }

            throw $e;
        }
    }
}