<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'student_class_enrollment_id',
        'user_id',
        'mark_method',
        'amount',
        'discount_amount',
        'paid_at',
        'payment_month',
        'payment_method',
        'status',
        'receipt_number',
        'reference_number',
        'is_synced',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'payment_month' => 'date',
        'is_synced' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        /*
        |--------------------------------------------------------------------------
        | Creating
        |--------------------------------------------------------------------------
        */

        static::creating(function ($payment) {

            // Default payment date
            if (empty($payment->paid_at)) {
                $payment->paid_at = now();
            }

            // Default payment month
            if (empty($payment->payment_month) && $payment->paid_at) {
                $payment->payment_month = date(
                    'Y-m-01',
                    strtotime($payment->paid_at)
                );
            }

            // Default payment method
            if (empty($payment->payment_method)) {
                $payment->payment_method = 'cash';
            }

            // Default status
            if (empty($payment->status)) {
                $payment->status = 'completed';
            }

            // Default discount
            if ($payment->discount_amount === null) {
                $payment->discount_amount = 0;
            }

            // Default sync status
            if ($payment->is_synced === null) {
                $payment->is_synced = true;
            }
        });


        /*
        |--------------------------------------------------------------------------
        | Created
        |--------------------------------------------------------------------------
        |
        | Create financial snapshot for completed payments.
        |
        | Student pays:
        |
        |     Class Fee + Hall Fee
        |
        | Class Fee:
        |
        |     Teacher + Organizer + Institution
        |
        | Hall Fee:
        |
        |     100% Institution
        |
        */

        static::created(function ($payment) {

            /*
            |--------------------------------------------------------------------------
            | Only completed payments create snapshots
            |--------------------------------------------------------------------------
            */

            if ($payment->status !== 'completed') {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Load Required Relations
            |--------------------------------------------------------------------------
            */

            $payment->loadMissing([
                'enrollment.studentClass',
            ]);

            $enrollment = $payment->enrollment;
            $studentClass = $enrollment?->studentClass;

            if (!$enrollment || !$studentClass) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Payment Configuration
            |--------------------------------------------------------------------------
            */

            $config = ClassPaymentConfig::query()
                ->where(
                    'student_class_id',
                    $studentClass->id
                )
                ->where('is_active', true)
                ->latest()
                ->first();

            if (!$config) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CLASS FEE
            |--------------------------------------------------------------------------
            |
            | final_fee already handles:
            |
            | - default class fee
            | - custom fee
            | - enrollment discount
            |
            */

            $classFee = (float) (
                $enrollment->final_fee ?? 0
            );


            /*
            |--------------------------------------------------------------------------
            | HALL FEE
            |--------------------------------------------------------------------------
            |
            | Get the active schedule pattern for this
            | class + category.
            |
            | If hall doesn't exist:
            |
            |     hall_fee = 0
            |
            | If hall_price is NULL:
            |
            |     hall_fee = 0
            |
            */

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

            $hallFee = (float) (
                $pattern?->hall?->hall_price ?? 0
            );


            /*
            |--------------------------------------------------------------------------
            | TOTAL FEE
            |--------------------------------------------------------------------------
            |
            | Student's total monthly fee:
            |
            |     Class Fee + Hall Fee
            |
            */

            $totalFee = round(
                $classFee + $hallFee,
                2
            );


            /*
            |--------------------------------------------------------------------------
            | ACTUAL PAYMENT
            |--------------------------------------------------------------------------
            |
            | This is the amount actually paid by the student.
            |
            | Example:
            |
            | Class Fee = 2000
            | Hall Fee  = 500
            | Paid      = 2500
            |
            */

            $paymentAmount = (float) $payment->amount;


            /*
            |--------------------------------------------------------------------------
            | SPLIT PERCENTAGES
            |--------------------------------------------------------------------------
            */

            $teacherPercentage = (float) (
                $config->teacher_percentage ?? 0
            );

            $organizerPercentage = (float) (
                $config->organizer_percentage ?? 0
            );

            $institutionPercentage = max(
                100 -
                (
                    $teacherPercentage +
                    $organizerPercentage
                ),
                0
            );


            /*
            |--------------------------------------------------------------------------
            | CLASS FEE SPLIT
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | Teacher and Organizer percentages apply ONLY
            | to the class fee.
            |
            | Hall fee is NOT included here.
            |
            */

            $teacherAmount = round(
                $classFee *
                $teacherPercentage /
                100,
                2
            );

            $organizerAmount = round(
                $classFee *
                $organizerPercentage /
                100,
                2
            );


            /*
            |--------------------------------------------------------------------------
            | INSTITUTION CLASS SHARE
            |--------------------------------------------------------------------------
            */

            $institutionClassAmount = round(
                $classFee
                - $teacherAmount
                - $organizerAmount,
                2
            );


            /*
            |--------------------------------------------------------------------------
            | HALL FEE = 100% INSTITUTION
            |--------------------------------------------------------------------------
            */

            $institutionAmount = round(
                $institutionClassAmount
                + $hallFee,
                2
            );


            /*
            |--------------------------------------------------------------------------
            | CREATE PAYMENT SPLIT SNAPSHOT
            |--------------------------------------------------------------------------
            */

            PaymentSplitSnapshot::create([

                /*
                |--------------------------------------------------------------------------
                | Payment
                |--------------------------------------------------------------------------
                */

                'payment_id' => $payment->id,

                /*
                |--------------------------------------------------------------------------
                | Fee Snapshot
                |--------------------------------------------------------------------------
                */

                'class_fee' => $classFee,

                'hall_fee' => $hallFee,

                'total_fee' => $totalFee,

                /*
                |--------------------------------------------------------------------------
                | Class / Enrollment
                |--------------------------------------------------------------------------
                */

                'student_class_id' => $studentClass->id,

                'student_class_enrollment_id' => $enrollment->id,

                /*
                |--------------------------------------------------------------------------
                | Payment Configuration
                |--------------------------------------------------------------------------
                */

                'class_payment_config_id' => $config->id,

                /*
                |--------------------------------------------------------------------------
                | People
                |--------------------------------------------------------------------------
                */

                'teacher_id' => $config->teacher_id,

                'organizer_id' => $config->organizer_id,

                'created_by' => $payment->user_id,

                /*
                |--------------------------------------------------------------------------
                | Payment Amount
                |--------------------------------------------------------------------------
                */

                'payment_amount' => $paymentAmount,

                /*
                |--------------------------------------------------------------------------
                | Percentages
                |--------------------------------------------------------------------------
                */

                'teacher_percentage' => $teacherPercentage,

                'organizer_percentage' => $organizerPercentage,

                'institution_percentage' => $institutionPercentage,

                /*
                |--------------------------------------------------------------------------
                | Split Amounts
                |--------------------------------------------------------------------------
                */

                'teacher_amount' => $teacherAmount,

                'organizer_amount' => $organizerAmount,

                'institution_amount' => $institutionAmount,

                /*
                |--------------------------------------------------------------------------
                | Payment Date
                |--------------------------------------------------------------------------
                */

                'payment_date' => $payment->paid_at,
            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(Student::class);
    }


    public function enrollment()
    {
        return $this->belongsTo(
            StudentClassEnrollment::class,
            'student_class_enrollment_id'
        );
    }


    public function collectedBy()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }


    public function splitSnapshot()
    {
        return $this->hasOne(
            PaymentSplitSnapshot::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Class fee only.
     */
    public function getExpectedAmountAttribute()
    {
        return $this->enrollment?->final_fee ?? 0;
    }


    /**
     * Total expected amount including hall fee.
     *
     * Uses snapshot when available.
     */
    public function getTotalExpectedAmountAttribute()
    {
        if ($this->splitSnapshot) {
            return (float) $this->splitSnapshot->total_fee;
        }

        return (float) $this->expected_amount;
    }


    /**
     * Payment balance.
     */
    public function getBalanceAttribute()
    {
        return max(
            $this->total_expected_amount - (float) $this->amount,
            0
        );
    }


    /**
     * Fully paid check.
     */
    public function getIsFullyPaidAttribute()
    {
        return (float) $this->amount >=
            (float) $this->total_expected_amount;
    }
}