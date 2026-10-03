<?php

namespace App\Services;

use App\Models\ClassCategory;
use App\Models\ClassCategoryFee;
use App\Models\StudentClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClassCategoryFeeService
{
    /**
     * Get class category fees.
     */
    public function getAll(array $filters = [])
    {
        $query = ClassCategoryFee::query()
            ->with([
                'studentClass.grade',
                'studentClass.subject',
                'studentClass.teacher',
                'category',
                'activeFeeOptions',
            ]);

        if (!empty($filters['student_class_id'])) {
            $query->where(
                'student_class_id',
                $filters['student_class_id']
            );
        }

        if (!empty($filters['class_category_id'])) {
            $query->where(
                'class_category_id',
                $filters['class_category_id']
            );
        }

        if (array_key_exists('is_active', $filters)) {
            $query->where(
                'is_active',
                $filters['is_active']
            );
        }

        return $query
            ->latest()
            ->paginate(
                isset($filters['per_page'])
                    ? (int) $filters['per_page']
                    : 10
            )
            ->appends($filters);
    }

    /**
     * Find category fee.
     */
    public function find($id)
    {
        return ClassCategoryFee::with([
            'studentClass.grade',
            'studentClass.subject',
            'studentClass.teacher',
            'category',
            'feeOptions',
        ])->findOrFail($id);
    }

    /**
     * Create category fee.
     *
     * Note:
     * This creates only the parent category-fee record.
     * Actual prices are stored in fee options.
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $studentClass = StudentClass::findOrFail(
                $data['student_class_id']
            );

            if (!$studentClass->is_active) {
                throw ValidationException::withMessages([
                    'student_class_id' => [
                        'Inactive class එකකට category fee add කරන්න බැහැ.'
                    ],
                ]);
            }

            $category = ClassCategory::findOrFail(
                $data['class_category_id']
            );

            if (!$category->is_active) {
                throw ValidationException::withMessages([
                    'class_category_id' => [
                        'Inactive category එකක් select කරන්න බැහැ.'
                    ],
                ]);
            }

            $exists = ClassCategoryFee::query()
                ->where(
                    'student_class_id',
                    $data['student_class_id']
                )
                ->where(
                    'class_category_id',
                    $data['class_category_id']
                )
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'class_category_id' => [
                        'This category is already assigned to this class.'
                    ],
                ]);
            }

            $classCategoryFee = ClassCategoryFee::create([
                'student_class_id' => $data['student_class_id'],
                'class_category_id' => $data['class_category_id'],
                'is_active' => array_key_exists(
                    'is_active',
                    $data
                )
                    ? (bool) $data['is_active']
                    : true,
                'note' => isset($data['note'])
                    ? $data['note']
                    : null,
            ]);

            /*
             * Once a category fee is assigned,
             * class becomes ongoing.
             */
            $studentClass->update([
                'is_ongoing' => true,
            ]);

            return $classCategoryFee->fresh([
                'studentClass',
                'category',
                'feeOptions',
            ]);
        });
    }

    /**
     * Update category fee.
     */
    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $classCategoryFee = ClassCategoryFee::findOrFail($id);

            $studentClass = StudentClass::findOrFail(
                $data['student_class_id']
            );

            if (!$studentClass->is_active) {
                throw ValidationException::withMessages([
                    'student_class_id' => [
                        'Inactive class එකකට category fee assign කරන්න බැහැ.'
                    ],
                ]);
            }

            $category = ClassCategory::findOrFail(
                $data['class_category_id']
            );

            if (!$category->is_active) {
                throw ValidationException::withMessages([
                    'class_category_id' => [
                        'Inactive category එකක් select කරන්න බැහැ.'
                    ],
                ]);
            }

            $exists = ClassCategoryFee::query()
                ->where(
                    'student_class_id',
                    $data['student_class_id']
                )
                ->where(
                    'class_category_id',
                    $data['class_category_id']
                )
                ->where(
                    'id',
                    '!=',
                    $classCategoryFee->id
                )
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'class_category_id' => [
                        'This category is already assigned to this class.'
                    ],
                ]);
            }

            $classCategoryFee->update([
                'student_class_id' => $data['student_class_id'],
                'class_category_id' => $data['class_category_id'],
                'is_active' => array_key_exists(
                    'is_active',
                    $data
                )
                    ? (bool) $data['is_active']
                    : $classCategoryFee->is_active,
                'note' => isset($data['note'])
                    ? $data['note']
                    : null,
            ]);

            return $classCategoryFee->fresh([
                'studentClass',
                'category',
                'feeOptions',
            ]);
        });
    }

    /**
     * Delete category fee.
     */
    public function delete($id)
    {
        $classCategoryFee = ClassCategoryFee::findOrFail($id);

        /*
         * Don't delete if fee options are already used.
         */
        if (
            $classCategoryFee->feeOptions()
                ->whereHas('enrollments')
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'message' => [
                    'This category fee cannot be deleted because its fee options are already used by student enrollments.'
                ],
            ]);
        }

        return $classCategoryFee->delete();
    }

    /**
     * Toggle active status.
     */
    public function toggleActive($id)
    {
        $classCategoryFee = ClassCategoryFee::findOrFail($id);

        $classCategoryFee->update([
            'is_active' => !$classCategoryFee->is_active,
        ]);

        return $classCategoryFee->fresh();
    }

    /**
     * Get category fees by class.
     */
    public function getByClass($studentClassId)
    {
        return ClassCategoryFee::query()
            ->with([
                'category',
                'activeFeeOptions',
            ])
            ->where(
                'student_class_id',
                $studentClassId
            )
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
    }
}