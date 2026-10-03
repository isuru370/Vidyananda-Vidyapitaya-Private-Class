<?php

namespace App\Http\Requests\StudentClassEnrollment;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentClassEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'exists:students,id',
            ],

            'student_class_id' => [
                'required',
                'exists:student_classes,id',
            ],

            'class_category_fee_id' => [
                'required',
                'exists:class_category_fees,id',
            ],

            'class_category_fee_option_id' => [
                'required',
                'exists:class_category_fee_options,id',
            ],

            'is_free_card' => [
                'nullable',
                'boolean',
            ],

            'enrolled_at' => [
                'nullable',
                'date',
            ],

            'note' => [
                'nullable',
                'string',
            ],
        ];
    }
}