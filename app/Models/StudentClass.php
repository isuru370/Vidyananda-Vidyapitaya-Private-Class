<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentClass extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class_name',
        'class_type',
        'medium',
        'teacher_id',
        'subject_id',
        'grade_id',
        'is_active',
        'is_ongoing',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_ongoing' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Class category fee associations.
     *
     * Example:
     * Theory
     * Revision
     * Paper
     */
    public function categoryFees()
    {
        return $this->hasMany(
            ClassCategoryFee::class,
            'student_class_id'
        );
    }

    /**
     * Class categories.
     *
     * Fee amounts are NOT stored in class_category_fees.
     * Fee amounts are stored in class_category_fee_options.
     */
    public function categories()
    {
        return $this->belongsToMany(
            ClassCategory::class,
            'class_category_fees',
            'student_class_id',
            'class_category_id'
        )
            ->withPivot([
                'id',
                'is_active',
                'deleted_at',
            ])
            ->withTimestamps()
            ->wherePivotNull('deleted_at');
    }

    /**
     * Master timetable patterns.
     *
     * Example:
     * 2026-01-01 → 2026-12-31
     * Monday → Hall 1
     * Wednesday → Hall 1
     */
    public function schedulePatterns()
    {
        return $this->hasMany(
            ClassSchedulePattern::class,
            'student_class_id'
        );
    }

    public function schedules()
    {
        return $this->hasMany(
            ClassSchedule::class
        );
    }

    public function attendances()
    {
        return $this->hasMany(
            ClassSchedule::class
        );
    }

    public function enrollments()
    {
        return $this->hasMany(
            StudentClassEnrollment::class
        );
    }

    public function paymentConfig()
    {
        return $this->hasOne(
            ClassPaymentConfig::class,
            'student_class_id'
        );
    }
}