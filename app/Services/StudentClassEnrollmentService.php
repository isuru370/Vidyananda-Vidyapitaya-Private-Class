<?php

namespace App\Services;

use App\Models\ClassCategoryFee;
use App\Models\ClassCategoryFeeOption;
use App\Models\StudentClass;
use App\Models\StudentClassEnrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentClassEnrollmentService
{
    /*
    |--------------------------------------------------------------------------
    | Create Enrollment
    |--------------------------------------------------------------------------
    */

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $studentClassId = $data['student_class_id'];
            $categoryFeeId = $data['class_category_fee_id'];
            $feeOptionId = $data['class_category_fee_option_id'];

            /*
        |--------------------------------------------------------------------------
        | Validate Class
        |--------------------------------------------------------------------------
        */

            $studentClass = StudentClass::query()
                ->where('id', $studentClassId)
                ->where('is_active', true)
                ->first();

            if (!$studentClass) {
                throw ValidationException::withMessages([
                    'student_class_id' => [
                        'Selected class is invalid or inactive.'
                    ],
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Validate Category Fee
        |--------------------------------------------------------------------------
        */

            $categoryFee = ClassCategoryFee::query()
                ->where('id', $categoryFeeId)
                ->where('student_class_id', $studentClassId)
                ->where('is_active', true)
                ->first();

            if (!$categoryFee) {
                throw ValidationException::withMessages([
                    'class_category_fee_id' => [
                        'Selected category fee is not assigned to this class or is inactive.'
                    ],
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Validate Fee Option
        |--------------------------------------------------------------------------
        */

            $feeOption = ClassCategoryFeeOption::query()
                ->where('id', $feeOptionId)
                ->where('class_category_fee_id', $categoryFee->id)
                ->where('is_active', true)
                ->first();

            if (!$feeOption) {
                throw ValidationException::withMessages([
                    'class_category_fee_option_id' => [
                        'Selected fee option is invalid or inactive.'
                    ],
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Check Existing Active Enrollment
        |--------------------------------------------------------------------------
        |
        | One student can have only ONE active enrollment
        | for the same:
        |
        | Student
        | + Class
        | + Category
        |
        | If the fee option changes, the old enrollment will be
        | deactivated and a NEW enrollment will be created.
        |
        */

            $existingActiveEnrollment = StudentClassEnrollment::query()
                ->where('student_id', $data['student_id'])
                ->where('student_class_id', $studentClassId)
                ->where('class_category_fee_id', $categoryFeeId)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            /*
        |--------------------------------------------------------------------------
        | Deactivate Existing Active Enrollment
        |--------------------------------------------------------------------------
        */

            if ($existingActiveEnrollment) {

                /*
            |--------------------------------------------------------------
            | If the exact same fee option is already active,
            | do not create another enrollment.
            |--------------------------------------------------------------
            */

                if (
                    (int) $existingActiveEnrollment->class_category_fee_option_id
                    === (int) $feeOption->id
                ) {
                    throw ValidationException::withMessages([
                        'class_category_fee_option_id' => [
                            'Student is already enrolled with this class category and fee option.'
                        ],
                    ]);
                }

                /*
            |--------------------------------------------------------------
            | Fee option changed.
            |
            | Keep the old enrollment as history and deactivate it.
            |--------------------------------------------------------------
            */

                $existingActiveEnrollment->update([
                    'is_active' => false,
                    'left_at' => !empty($data['enrolled_at'])
                        ? $data['enrolled_at']
                        : now()->toDateString(),
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Create New Enrollment
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Never update/restore an old enrollment here.
        |
        | Every class/category/fee-option change creates a NEW
        | enrollment record.
        |
        */

            $enrollment = StudentClassEnrollment::create([
                'student_id' => $data['student_id'],

                'student_class_id' => $studentClassId,

                'class_category_fee_id' => $categoryFeeId,

                'class_category_fee_option_id' => $feeOption->id,

                'is_active' => true,

                'is_free_card' => !empty($data['is_free_card'])
                    ? true
                    : false,

                'enrolled_at' => !empty($data['enrolled_at'])
                    ? $data['enrolled_at']
                    : now()->toDateString(),

                'left_at' => null,

                'note' => $data['note'] ?? null,
            ]);

            /*
        |--------------------------------------------------------------------------
        | Return New Enrollment
        |--------------------------------------------------------------------------
        */

            return $enrollment->load([
                'student',
                'studentClass',
                'classCategoryFee.category',
                'classCategoryFeeOption',
            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Update Enrollment
    |--------------------------------------------------------------------------
    */

    public function update(
        StudentClassEnrollment $enrollment,
        array $data
    ) {
        return DB::transaction(function () use (
            $enrollment,
            $data
        ) {

            /*
            |--------------------------------------------------------------------------
            | Student Class Cannot Be Changed
            |--------------------------------------------------------------------------
            */

            $studentClassId = $enrollment->student_class_id;

            /*
            |--------------------------------------------------------------------------
            | Category Fee
            |--------------------------------------------------------------------------
            */

            $categoryFeeId = isset($data['class_category_fee_id'])
                ? $data['class_category_fee_id']
                : $enrollment->class_category_fee_id;

            $categoryFee = ClassCategoryFee::query()
                ->where('id', $categoryFeeId)
                ->where('student_class_id', $studentClassId)
                ->where('is_active', true)
                ->first();

            if (!$categoryFee) {
                throw ValidationException::withMessages([
                    'class_category_fee_id' => [
                        'Selected category fee is not assigned to this class or is inactive.'
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Fee Option
            |--------------------------------------------------------------------------
            */

            $feeOptionId = isset($data['class_category_fee_option_id'])
                ? $data['class_category_fee_option_id']
                : $enrollment->class_category_fee_option_id;

            $feeOption = ClassCategoryFeeOption::query()
                ->where('id', $feeOptionId)
                ->where('class_category_fee_id', $categoryFee->id)
                ->where('is_active', true)
                ->first();

            if (!$feeOption) {
                throw ValidationException::withMessages([
                    'class_category_fee_option_id' => [
                        'Selected fee option is invalid or inactive.'
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Duplicate Enrollment Check
            |--------------------------------------------------------------------------
            */

            $existing = StudentClassEnrollment::withTrashed()
                ->where('student_id', $enrollment->student_id)
                ->where('student_class_id', $studentClassId)
                ->where('class_category_fee_id', $categoryFeeId)
                ->where('id', '!=', $enrollment->id)
                ->first();

            if (
                $existing &&
                !$existing->trashed() &&
                $existing->is_active
            ) {
                throw ValidationException::withMessages([
                    'class_category_fee_id' => [
                        'Student is already enrolled in this class category.'
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Update
            |--------------------------------------------------------------------------
            */

            $isActive = array_key_exists(
                'is_active',
                $data
            )
                ? (bool) $data['is_active']
                : (bool) $enrollment->is_active;

            $isFreeCard = array_key_exists(
                'is_free_card',
                $data
            )
                ? (bool) $data['is_free_card']
                : (bool) $enrollment->is_free_card;

            $enrolledAt = array_key_exists(
                'enrolled_at',
                $data
            )
                ? $data['enrolled_at']
                : $enrollment->enrolled_at;

            $leftAt = array_key_exists(
                'left_at',
                $data
            )
                ? $data['left_at']
                : $enrollment->left_at;

            if ($isActive) {
                $leftAt = null;
            }

            if (!$isActive && empty($leftAt)) {
                $leftAt = now()->toDateString();
            }

            $enrollment->update([
                'class_category_fee_id' => $categoryFee->id,
                'class_category_fee_option_id' => $feeOption->id,

                'is_active' => $isActive,
                'is_free_card' => $isFreeCard,

                'enrolled_at' => $enrolledAt,
                'left_at' => $leftAt,

                'note' => isset($data['note'])
                    ? $data['note']
                    : $enrollment->note,
            ]);

            return $enrollment->fresh([
                'student',
                'studentClass',
                'classCategoryFee.category',
                'classCategoryFeeOption',
            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Activate
    |--------------------------------------------------------------------------
    */

    public function activate(
        StudentClassEnrollment $enrollment
    ) {
        if ($enrollment->is_active) {
            throw ValidationException::withMessages([
                'enrollment' => [
                    'Student enrollment is already active.'
                ],
            ]);
        }

        $enrollment->update([
            'is_active' => true,
            'left_at' => null,
        ]);

        return $enrollment->fresh();
    }


    /*
    |--------------------------------------------------------------------------
    | Deactivate
    |--------------------------------------------------------------------------
    */

    public function deactivate(
        StudentClassEnrollment $enrollment
    ) {
        if (!$enrollment->is_active) {
            throw ValidationException::withMessages([
                'enrollment' => [
                    'Student enrollment is already inactive.'
                ],
            ]);
        }

        $enrollment->update([
            'is_active' => false,
            'left_at' => now()->toDateString(),
        ]);

        return $enrollment->fresh();
    }


    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(
        StudentClassEnrollment $enrollment
    ) {
        if ($enrollment->is_active) {
            return $this->deactivate($enrollment);
        }

        return $this->activate($enrollment);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(
        StudentClassEnrollment $enrollment
    ) {
        if ($enrollment->payments()->exists()) {
            throw ValidationException::withMessages([
                'enrollment' => [
                    'Cannot delete enrollment because payments already exist.'
                ],
            ]);
        }

        $enrollment->delete();

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore($id)
    {
        return DB::transaction(function () use ($id) {

            $enrollment = StudentClassEnrollment::withTrashed()
                ->findOrFail($id);

            if (!$enrollment->trashed()) {
                throw ValidationException::withMessages([
                    'enrollment' => [
                        'Enrollment is not deleted.'
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Check duplicate active enrollment
            |--------------------------------------------------------------------------
            */

            $existing = StudentClassEnrollment::query()
                ->where('student_id', $enrollment->student_id)
                ->where(
                    'student_class_id',
                    $enrollment->student_class_id
                )
                ->where(
                    'class_category_fee_id',
                    $enrollment->class_category_fee_id
                )
                ->where('id', '!=', $enrollment->id)
                ->where('is_active', true)
                ->exists();

            if ($existing) {
                throw ValidationException::withMessages([
                    'enrollment' => [
                        'An active enrollment already exists for this class category.'
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Validate current Fee Option
            |--------------------------------------------------------------------------
            */

            $this->validateFeeOption(
                $enrollment->student_class_id,
                $enrollment->class_category_fee_id,
                $enrollment->class_category_fee_option_id
            );

            $enrollment->restore();

            $enrollment->update([
                'is_active' => true,
                'left_at' => null,
            ]);

            return $enrollment->fresh([
                'student',
                'studentClass',
                'classCategoryFee.category',
                'classCategoryFeeOption',
            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Find Enrollment
    |--------------------------------------------------------------------------
    */

    public function find($id)
    {
        return StudentClassEnrollment::with([
            'student',
            'studentClass.grade',
            'studentClass.subject',
            'studentClass.teacher',
            'classCategoryFee.category',
            'classCategoryFeeOption',
            'payments',
        ])->findOrFail($id);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Student Enrollments
    |--------------------------------------------------------------------------
    */

    public function getStudentEnrollments(
        $studentId,
        $activeOnly = false
    ) {
        $query = StudentClassEnrollment::query()
            ->with([
                'studentClass.teacher',
                'studentClass.grade',
                'studentClass.subject',
                'classCategoryFee.category',
                'classCategoryFeeOption',
            ])
            ->where('student_id', $studentId);

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query
            ->orderByDesc('is_active')
            ->latest()
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Get Fee Options For Enrollment
    |--------------------------------------------------------------------------
    */

    public function getFeeOptions(
        $studentClassId,
        $classCategoryFeeId
    ) {
        $categoryFee = ClassCategoryFee::query()
            ->where('id', $classCategoryFeeId)
            ->where('student_class_id', $studentClassId)
            ->where('is_active', true)
            ->first();

        if (!$categoryFee) {
            throw ValidationException::withMessages([
                'class_category_fee_id' => [
                    'Selected category fee is invalid or inactive.'
                ],
            ]);
        }

        return ClassCategoryFeeOption::query()
            ->where('class_category_fee_id', $categoryFee->id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Get Student Classes
    |--------------------------------------------------------------------------
    */

    public function getStudentClasses($studentId)
    {
        return $this->getStudentEnrollments($studentId);
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Fee Option
    |--------------------------------------------------------------------------
    */

    protected function validateFeeOption(
        $studentClassId,
        $classCategoryFeeId,
        $feeOptionId
    ) {
        $categoryFee = ClassCategoryFee::query()
            ->where('id', $classCategoryFeeId)
            ->where('student_class_id', $studentClassId)
            ->where('is_active', true)
            ->first();

        if (!$categoryFee) {
            throw ValidationException::withMessages([
                'class_category_fee_id' => [
                    'Selected category fee is not assigned to this class or is inactive.'
                ],
            ]);
        }

        $option = ClassCategoryFeeOption::query()
            ->where('id', $feeOptionId)
            ->where('class_category_fee_id', $categoryFee->id)
            ->where('is_active', true)
            ->first();

        if (!$option) {
            throw ValidationException::withMessages([
                'class_category_fee_option_id' => [
                    'Selected fee option is invalid or inactive.'
                ],
            ]);
        }

        return $option;
    }


    /*
    |--------------------------------------------------------------------------
    | Category Wise Payment Students
    |--------------------------------------------------------------------------
    */

    public function classCategoryWisePaymentStudent(
        $classId,
        $classCategoryFeeId,
        $year,
        $month,
        $perPage = 50
    ) {
        $query = StudentClassEnrollment::query()
            ->with([
                'student',
                'classCategoryFee.category',
                'classCategoryFeeOption',
                'payments' => function ($query) use ($year, $month) {
                    $query->whereYear('payment_month', $year)
                        ->whereMonth('payment_month', $month);
                },
            ])
            ->where('student_class_id', $classId)
            ->where('class_category_fee_id', $classCategoryFeeId)
            ->where('is_active', true);

        $paginator = $query
            ->orderBy('id')
            ->paginate($perPage);

        $paginator->getCollection()->transform(
            function ($enrollment) {

                $paidAmount = $enrollment->payments->sum(
                    'amount'
                );

                $finalFee = $enrollment->is_free_card
                    ? 0
                    : (float) $enrollment->getSelectedFee();

                return [
                    'enrollment_id' => $enrollment->id,

                    'student_id' => $enrollment->student_id,

                    'student' => $enrollment->student
                        ? [
                            'id' => $enrollment->student->id,
                            'custom_id' => $enrollment->student->custom_id,
                            'initial_name' => $enrollment->student->initial_name,
                            'full_name' => $enrollment->student->full_name,
                            'mobile' => $enrollment->student->mobile,
                        ]
                        : null,

                    'class_category_fee_id' =>
                    $enrollment->class_category_fee_id,

                    'class_category_fee_option_id' =>
                    $enrollment->class_category_fee_option_id,

                    'category_name' =>
                    optional(
                        $enrollment->classCategoryFee
                    )->category
                        ? $enrollment->classCategoryFee->category->category_name
                        : null,

                    'fee_option' =>
                    $enrollment->classCategoryFeeOption
                        ? [
                            'id' =>
                            $enrollment->classCategoryFeeOption->id,

                            'label' =>
                            $enrollment->classCategoryFeeOption->label,

                            'fee' =>
                            (float) $enrollment->classCategoryFeeOption->fee,
                        ]
                        : null,

                    'is_free_card' =>
                    (bool) $enrollment->is_free_card,

                    'final_fee' => $finalFee,

                    'paid_amount' => (float) $paidAmount,

                    'balance' => max(
                        $finalFee - $paidAmount,
                        0
                    ),

                    'payment_status' =>
                    $paidAmount <= 0
                        ? 'unpaid'
                        : (
                            $paidAmount < $finalFee
                            ? 'partial'
                            : 'paid'
                        ),

                    'registered_date' =>
                    $enrollment->enrolled_at
                        ? $enrollment->enrolled_at->format('Y-m-d')
                        : null,
                ];
            }
        );

        return [
            'students' => $paginator,
            'pagination' => [
                'current_page' =>
                $paginator->currentPage(),

                'last_page' =>
                $paginator->lastPage(),

                'per_page' =>
                $paginator->perPage(),

                'total' =>
                $paginator->total(),
            ],
        ];
    }
}
