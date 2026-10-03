<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class CategoryStudentsExport implements FromCollection
{
    protected $enrollments;
    protected $studentClass;
    protected $classCategory;

    public function __construct(
        Collection $enrollments,
        $studentClass,
        $classCategory
    ) {
        $this->enrollments = $enrollments;
        $this->studentClass = $studentClass;
        $this->classCategory = $classCategory;
    }

    public function collection()
    {
        $rows = collect();

        $rows->push([
            'Class',
            $this->studentClass->class_name
        ]);

        $rows->push([
            'Grade',
            optional($this->studentClass->grade)->grade_name ?: '-'
        ]);

        $rows->push([
            'Subject',
            optional($this->studentClass->subject)->subject_name ?: '-'
        ]);

        $rows->push([
            'Teacher',
            optional($this->studentClass->teacher)->initials
                ?: optional($this->studentClass->teacher)->full_name
                ?: '-'
        ]);

        $rows->push([
            'Category',
            $this->classCategory->category_name
        ]);

        $rows->push([]);

        $rows->push([
            '#',
            'Student ID / QR',
            'Initial Name',
            'Full Name',
            'Mobile',
            'Fee Option',
            'Fee Option Fee',
            'Final Fee',
            'Status',
        ]);

        foreach ($this->enrollments as $index => $enrollment) {
            $student = $enrollment->student;

            $studentCode = '-';

            if (
                $student &&
                $student->permanent_qr_active &&
                !empty($student->custom_id) &&
                $student->custom_id != '0'
            ) {
                $studentCode = $student->custom_id;
            } else {
                $studentCode = $student
                    ? ($student->temporary_qr_code ?: '-')
                    : '-';
            }

            $feeOption = $enrollment->classCategoryFeeOption;

            if ($enrollment->is_free_card) {
                $feeType = 'Free Card';
                $feeOptionFee = 0;
                $finalFee = 0;
            } else {
                $feeType = $feeOption
                    ? $feeOption->label
                    : 'N/A';

                $feeOptionFee = $feeOption
                    ? (float) $feeOption->fee
                    : 0;

                $finalFee = (float) $enrollment->final_fee;
            }

            $rows->push([
                $index + 1,
                $studentCode,
                $student ? ($student->initial_name ?: '-') : '-',
                $student ? ($student->full_name ?: '-') : '-',
                $student ? ($student->mobile ?: '-') : '-',
                $feeType,
                $feeOptionFee,
                $finalFee,
                $enrollment->is_active
                    ? 'Active'
                    : 'Inactive',
            ]);
        }

        return $rows;
    }
}
