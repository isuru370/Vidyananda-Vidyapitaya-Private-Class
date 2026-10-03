<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentSplitSnapshot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payment_id',

        // Fee snapshot
        'class_fee',
        'hall_fee',
        'total_fee',

        // Class / Enrollment
        'student_class_id',
        'student_class_enrollment_id',

        // Payment configuration
        'class_payment_config_id',

        // People involved
        'teacher_id',
        'organizer_id',
        'created_by',

        // Payment
        'payment_amount',

        // Percentage snapshot
        'teacher_percentage',
        'organizer_percentage',
        'institution_percentage',

        // Amount snapshot
        'teacher_amount',
        'organizer_amount',
        'institution_amount',

        // Payment date
        'payment_date',
    ];

    protected $casts = [
        // Fee snapshot
        'class_fee' => 'decimal:2',
        'hall_fee' => 'decimal:2',
        'total_fee' => 'decimal:2',

        // Payment
        'payment_amount' => 'decimal:2',

        // Percentage snapshot
        'teacher_percentage' => 'decimal:2',
        'organizer_percentage' => 'decimal:2',
        'institution_percentage' => 'decimal:2',

        // Amount snapshot
        'teacher_amount' => 'decimal:2',
        'organizer_amount' => 'decimal:2',
        'institution_amount' => 'decimal:2',

        // Date
        'payment_date' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function payment()
    {
        return $this->belongsTo(
            Payment::class,
            'payment_id'
        );
    }

    public function studentClass()
    {
        return $this->belongsTo(
            StudentClass::class,
            'student_class_id'
        );
    }

    public function enrollment()
    {
        return $this->belongsTo(
            StudentClassEnrollment::class,
            'student_class_enrollment_id'
        );
    }

    public function paymentConfig()
    {
        return $this->belongsTo(
            ClassPaymentConfig::class,
            'class_payment_config_id'
        );
    }

    public function teacher()
    {
        return $this->belongsTo(
            Teacher::class,
            'teacher_id'
        );
    }

    public function organizer()
    {
        return $this->belongsTo(
            Organizer::class,
            'organizer_id'
        );
    }

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
