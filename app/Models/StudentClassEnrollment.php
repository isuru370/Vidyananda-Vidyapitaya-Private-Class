<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentClassEnrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'student_class_id',
        'class_category_fee_id',
        'class_category_fee_option_id',
        'is_active',
        'is_free_card',
        'enrolled_at',
        'left_at',
        'note',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_free_card' => 'boolean',
        'enrolled_at' => 'date',
        'left_at' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(
            Student::class
        );
    }

    public function studentClass()
    {
        return $this->belongsTo(
            StudentClass::class
        );
    }

    public function classCategoryFee()
    {
        return $this->belongsTo(
            ClassCategoryFee::class,
            'class_category_fee_id'
        );
    }

    public function classCategoryFeeOption()
    {
        return $this->belongsTo(
            ClassCategoryFeeOption::class,
            'class_category_fee_option_id'
        );
    }

    public function category()
    {
        return $this->hasOneThrough(
            ClassCategory::class,
            ClassCategoryFee::class,
            'id',
            'id',
            'class_category_fee_id',
            'class_category_id'
        );
    }

    public function payments()
    {
        return $this->hasMany(
            Payment::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fee
    |--------------------------------------------------------------------------
    */

    public function getFinalFeeAttribute()
    {
        if ($this->is_free_card) {
            return 0;
        }

        return $this->getSelectedFee();
    }

    public function getSelectedFee()
    {
        if (!$this->classCategoryFeeOption) {
            return 0;
        }

        return (float) $this->classCategoryFeeOption->fee;
    }

    /*
    |--------------------------------------------------------------------------
    | Payment
    |--------------------------------------------------------------------------
    */

    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return max(
            $this->final_fee - $this->paid_amount,
            0
        );
    }

    public function getPaymentStatusAttribute()
    {
        if ($this->paid_amount <= 0) {
            return 'unpaid';
        }

        return 'paid';
    }
}