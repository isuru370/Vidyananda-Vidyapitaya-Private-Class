<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentClassEnrollment;
use App\Models\ClassSchedule;
use App\Models\StudentTute;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StudentClassManagementService
{
    /*
    |--------------------------------------------------------------------------
    | Get Student Classes
    |--------------------------------------------------------------------------
    */

    public function getStudentClasses(int $studentId): Collection
    {
        $student = Student::with([
            'enrollments.studentClass.teacher',
            'enrollments.studentClass.grade',
            'enrollments.classCategoryFee.category',
        ])->findOrFail($studentId);

        return $student->enrollments->map(function ($enrollment) {
            return [
                'enrollment_id' => $enrollment->id,
                'class_id' => $enrollment->student_class_id,

                'class_name' =>
                $enrollment->studentClass?->class_name ?? 'N/A',

                'class_type' =>
                $enrollment->studentClass?->class_type ?? 'N/A',

                'medium' =>
                $enrollment->studentClass?->medium ?? 'N/A',

                'grade_name' =>
                $enrollment->studentClass?->grade?->grade_name ?? 'N/A',

                'teacher_initials' =>
                $enrollment->studentClass?->teacher?->initials ?? 'N/A',

                'category_name' =>
                $enrollment->classCategoryFee?->category?->category_name ?? 'N/A',

                'class_category_fee_id' =>
                $enrollment->class_category_fee_id,

                'fee' =>
                $enrollment->classCategoryFee?->fee ?? 0,

                'is_active' =>
                $enrollment->is_active,

                'custom_fee' =>
                $enrollment->custom_fee,

                'discount_percentage' =>
                $enrollment->discount_percentage,

                'final_fee' =>
                $enrollment->final_fee,

                'payment_status' =>
                $enrollment->payment_status,

                'enrolled_at' =>
                $enrollment->enrolled_at?->format('Y-m-d'),

                'left_at' =>
                $enrollment->left_at?->format('Y-m-d'),

                'note' =>
                $enrollment->note,
            ];
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Get Class Details
    |--------------------------------------------------------------------------
    */

    public function getClassDetails(
        int $studentId,
        int $studentClassId,
        int $enrollmentId
    ): array {
        $enrollment = StudentClassEnrollment::with([
            'student',
            'studentClass.teacher',
            'studentClass.grade',
            'classCategoryFee.category',
        ])
            ->where('id', $enrollmentId)
            ->where('student_id', $studentId)
            ->where('student_class_id', $studentClassId)
            ->firstOrFail();

        return [
            'student' =>
            $enrollment->student,

            'enrollment' =>
            $enrollment,

            'class' =>
            $enrollment->studentClass,

            'category' =>
            $enrollment->classCategoryFee?->category,

            'class_category_fee_id' =>
            $enrollment->class_category_fee_id,

            'teacher' =>
            $enrollment->studentClass?->teacher,

            'grade' =>
            $enrollment->studentClass?->grade,

            'fee' =>
            $enrollment->classCategoryFee?->fee ?? 0,

            'final_fee' =>
            $enrollment->final_fee,

            'payment_status' =>
            $enrollment->payment_status,

            'enrolled_at' =>
            $enrollment->enrolled_at,

            'left_at' =>
            $enrollment->left_at,

            'is_active' =>
            $enrollment->is_active,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Get Payment Details
    |--------------------------------------------------------------------------
    |
    | Payment is restricted to:
    | Student
    | + Student Class
    | + Enrollment
    |
    | Payments before the enrollment month are ignored.
    |
    */

    public function getPaymentDetails(
        int $studentId,
        int $studentClassId,
        int $enrollmentId
    ): array {

        /*
    |--------------------------------------------------------------------------
    | Enrollment
    |--------------------------------------------------------------------------
    */

        $enrollment = StudentClassEnrollment::with([
            'student',
            'studentClass.teacher',
            'studentClass.grade',
            'classCategoryFee.category',

            'payments' => function ($query) {
                $query
                    ->orderByDesc('payment_month')
                    ->orderByDesc('paid_at')
                    ->orderByDesc('id');
            },
        ])
            ->where('id', $enrollmentId)
            ->where('student_id', $studentId)
            ->where('student_class_id', $studentClassId)
            ->firstOrFail();


        /*
    |--------------------------------------------------------------------------
    | Enrollment Date
    |--------------------------------------------------------------------------
    */

        $enrollmentDate = $enrollment->enrolled_at;

        $enrollmentMonth = $enrollmentDate
            ? $enrollmentDate->copy()->startOfMonth()
            : null;


        /*
    |--------------------------------------------------------------------------
    | Payment Records
    |--------------------------------------------------------------------------
    |
    | payment_month = Which month this payment belongs to
    | paid_at       = Actual date payment was made
    | receipt_number = Receipt number
    |
    */

        $payments = $enrollment->payments
            ->filter(function ($payment) use ($enrollmentMonth) {

                /*
            | Payment month is required to identify
            | which month the payment belongs to.
            */
                if (!$payment->payment_month) {
                    return false;
                }

                /*
            | Do not show payments before enrollment month.
            */
                if ($enrollmentMonth) {

                    return $payment->payment_month
                        ->copy()
                        ->startOfMonth()
                        ->greaterThanOrEqualTo($enrollmentMonth);
                }

                return true;
            })
            ->values();


        /*
    |--------------------------------------------------------------------------
    | Monthly Fee
    |--------------------------------------------------------------------------
    |
    | final_fee is treated as the monthly fee for this enrollment.
    |
    */

        $monthlyFee = (float) $enrollment->final_fee;


        /*
    |--------------------------------------------------------------------------
    | Create Month Wise Payment Data
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | January 2026
    | February 2026
    | March 2026
    |
    | Each month will show:
    | - Expected fee
    | - Paid amount
    | - Balance
    | - Status
    | - Payment records
    |
    */

        $monthlyPayments = collect();


        if ($enrollmentMonth) {

            /*
        | End month
        |
        | Normally current month.
        | If student has left, stop at left month.
        */

            $endMonth = now()->startOfMonth();

            if ($enrollment->left_at) {

                $leftMonth = $enrollment->left_at
                    ->copy()
                    ->startOfMonth();

                if ($leftMonth->lessThan($endMonth)) {
                    $endMonth = $leftMonth;
                }
            }


            /*
        |--------------------------------------------------------------------------
        | Generate Every Month From Enrollment
        |--------------------------------------------------------------------------
        */

            $currentMonth = $enrollmentMonth->copy();

            while ($currentMonth->lessThanOrEqualTo($endMonth)) {

                $monthKey = $currentMonth->format('Y-m');


                /*
            | Get payments for this particular month.
            */
                $monthPayments = $payments
                    ->filter(function ($payment) use ($monthKey) {

                        return $payment->payment_month
                            ->copy()
                            ->format('Y-m') === $monthKey;
                    })
                    ->values();


                /*
            |--------------------------------------------------------------------------
            | Monthly Paid Amount
            |--------------------------------------------------------------------------
            */

                $paidAmount = (float) $monthPayments->sum('amount');


                /*
            |--------------------------------------------------------------------------
            | Monthly Discount
            |--------------------------------------------------------------------------
            */

                $discountAmount = (float) $monthPayments
                    ->sum('discount_amount');


                /*
            |--------------------------------------------------------------------------
            | Monthly Balance
            |--------------------------------------------------------------------------
            */

                $balance = max(
                    $monthlyFee - $paidAmount,
                    0
                );


                /*
            |--------------------------------------------------------------------------
            | Monthly Payment Status
            |--------------------------------------------------------------------------
            */

                if ($paidAmount >= $monthlyFee) {

                    $paymentStatus = 'paid';
                } else {

                    $paymentStatus = 'unpaid';
                }


                /*
            |--------------------------------------------------------------------------
            | Monthly Payment Data
            |--------------------------------------------------------------------------
            */

                $monthlyPayments->push([

                    'month' => $currentMonth->copy(),

                    'month_key' => $monthKey,

                    'month_name' => $currentMonth->format('F Y'),

                    'expected_fee' => $monthlyFee,

                    'paid_amount' => $paidAmount,

                    'discount_amount' => $discountAmount,

                    'balance' => $balance,

                    'payment_status' => $paymentStatus,

                    /*
                | All payment records for this month.
                |
                | This is important because one month can
                | have multiple payments.
                */
                    'payments' => $monthPayments,

                ]);


                /*
            | Next month
            */
                $currentMonth->addMonth();
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Overall Summary
    |--------------------------------------------------------------------------
    */

        $totalPaidAmount = (float) $payments->sum('amount');

        $totalDiscountAmount = (float) $payments->sum('discount_amount');

        $totalExpectedAmount = (float) $monthlyPayments
            ->sum('expected_fee');

        $totalBalance = max(
            $totalExpectedAmount - $totalPaidAmount,
            0
        );


        /*
    |--------------------------------------------------------------------------
    | Overall Payment Status
    |--------------------------------------------------------------------------
    */

        if ($totalPaidAmount <= 0) {

            $paymentStatus = 'unpaid';
        } elseif ($totalBalance > 0) {

            $paymentStatus = 'partial';
        } else {

            $paymentStatus = 'paid';
        }


        /*
    |--------------------------------------------------------------------------
    | Return Data
    |--------------------------------------------------------------------------
    */

        return [

            /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

            'student' =>
            $enrollment->student,


            /*
        |--------------------------------------------------------------------------
        | Enrollment
        |--------------------------------------------------------------------------
        */

            'enrollment' =>
            $enrollment,


            /*
        |--------------------------------------------------------------------------
        | Class
        |--------------------------------------------------------------------------
        */

            'class' =>
            $enrollment->studentClass,


            /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

            'category' =>
            $enrollment->classCategoryFee?->category,


            /*
        |--------------------------------------------------------------------------
        | Teacher
        |--------------------------------------------------------------------------
        */

            'teacher' =>
            $enrollment->studentClass?->teacher,


            /*
        |--------------------------------------------------------------------------
        | Grade
        |--------------------------------------------------------------------------
        */

            'grade' =>
            $enrollment->studentClass?->grade,


            /*
        |--------------------------------------------------------------------------
        | Category Fee ID
        |--------------------------------------------------------------------------
        */

            'class_category_fee_id' =>
            $enrollment->class_category_fee_id,


            /*
        |--------------------------------------------------------------------------
        | Enrollment Dates
        |--------------------------------------------------------------------------
        */

            'enrolled_at' =>
            $enrollment->enrolled_at,

            'left_at' =>
            $enrollment->left_at,


            /*
        |--------------------------------------------------------------------------
        | Monthly Fee
        |--------------------------------------------------------------------------
        */

            'monthly_fee' =>
            $monthlyFee,


            /*
        |--------------------------------------------------------------------------
        | Raw Payment Records
        |--------------------------------------------------------------------------
        */

            'payments' =>
            $payments,


            /*
        |--------------------------------------------------------------------------
        | Month Wise Payment Data
        |--------------------------------------------------------------------------
        */

            'monthly_payments' =>
            $monthlyPayments,


            /*
        |--------------------------------------------------------------------------
        | Overall Summary
        |--------------------------------------------------------------------------
        */

            'expected_fee' =>
            $totalExpectedAmount,

            'paid_amount' =>
            $totalPaidAmount,

            'discount_amount' =>
            $totalDiscountAmount,

            'balance' =>
            $totalBalance,

            'payment_status' =>
            $paymentStatus,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Get Attendance Details
    |--------------------------------------------------------------------------
    |
    | Class days:
    |   ClassSchedule.status = completed
    |
    | Student attendance:
    |   StudentAttendance.student_id = selected student
    |
    | Important:
    |   Class days before enrolled_at are NOT counted.
    |
    | Also restricted by:
    |   student_class_id
    |   class_category_fee_id
    |
    */

    public function getAttendanceDetails(
        int $studentId,
        int $studentClassId,
        int $enrollmentId
    ): array {

        /*
    |--------------------------------------------------------------------------
    | Get Enrollment
    |--------------------------------------------------------------------------
    */

        $enrollment = StudentClassEnrollment::with([
            'student',
            'studentClass.teacher',
            'studentClass.grade',
            'classCategoryFee.category',
        ])
            ->where('id', $enrollmentId)
            ->where('student_id', $studentId)
            ->where('student_class_id', $studentClassId)
            ->firstOrFail();


        /*
    |--------------------------------------------------------------------------
    | Enrollment Period
    |--------------------------------------------------------------------------
    */

        $enrolledAt = $enrollment->enrolled_at
            ? $enrollment->enrolled_at->copy()->startOfDay()
            : null;

        $leftAt = $enrollment->left_at
            ? $enrollment->left_at->copy()->endOfDay()
            : null;


        /*
    |--------------------------------------------------------------------------
    | Get Completed Class Schedules
    |--------------------------------------------------------------------------
    |
    | Only completed classes are counted.
    |
    */

        $schedules = ClassSchedule::query()
            ->where('student_class_id', $studentClassId)

            ->where(
                'class_category_fee_id',
                $enrollment->class_category_fee_id
            )

            ->where('status', 'completed')

            ->where('is_active', true)

            /*
        |--------------------------------------------------------------------------
        | From Enrollment Date
        |--------------------------------------------------------------------------
        */

            ->when($enrolledAt, function ($query) use ($enrolledAt) {

                $query->whereDate(
                    'class_date',
                    '>=',
                    $enrolledAt->toDateString()
                );
            })

            /*
        |--------------------------------------------------------------------------
        | Until Left Date
        |--------------------------------------------------------------------------
        */

            ->when($leftAt, function ($query) use ($leftAt) {

                $query->whereDate(
                    'class_date',
                    '<=',
                    $leftAt->toDateString()
                );
            })

            /*
        |--------------------------------------------------------------------------
        | Student Attendance
        |--------------------------------------------------------------------------
        */

            ->with([
                'studentAttendances' => function ($query) use (
                    $studentId,
                    $enrollmentId
                ) {

                    $query
                        ->where('student_id', $studentId)
                        ->where(
                            'student_class_enrollment_id',
                            $enrollmentId
                        );
                },
            ])

            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | Daily Attendance Data
    |--------------------------------------------------------------------------
    */

        $attendanceData = $schedules
            ->map(function ($schedule) {

                $attended = $schedule
                    ->studentAttendances
                    ->isNotEmpty();

                return [

                    'schedule_id' => $schedule->id,

                    'date' => $schedule->class_date
                        ? $schedule->class_date->format('Y-m-d')
                        : null,

                    'start_time' => $schedule->start_time,

                    'end_time' => $schedule->end_time,

                    'status' => $attended
                        ? 'attended'
                        : 'absent',

                    'attended' => $attended,
                ];
            })
            ->values();


        /*
    |--------------------------------------------------------------------------
    | Monthly Attendance
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | 2025-08
    | total_days    = 8
    | attended_days = 6
    | absent_days   = 2
    |
    */

        $monthlyAttendance = $attendanceData
            ->groupBy(function ($attendance) {

                return Carbon::parse(
                    $attendance['date']
                )->format('Y-m');
            })
            ->map(function ($monthData, $month) {

                $total = $monthData->count();

                $attended = $monthData
                    ->where('attended', true)
                    ->count();

                $absent = $monthData
                    ->where('attended', false)
                    ->count();

                $percentage = $total > 0
                    ? round(
                        ($attended / $total) * 100,
                        2
                    )
                    : 0;

                return [

                    'month' => $month,

                    'month_name' => Carbon::createFromFormat(
                        'Y-m',
                        $month
                    )->format('M Y'),

                    'total_days' => $total,

                    'attended_days' => $attended,

                    'absent_days' => $absent,

                    'attendance_percentage' => $percentage,

                    'attendance_data' => $monthData->values(),
                ];
            })
            ->sortBy('month')
            ->values();


        /*
    |--------------------------------------------------------------------------
    | Overall Attendance Summary
    |--------------------------------------------------------------------------
    */

        $totalDays = $attendanceData->count();

        $attendedDays = $attendanceData
            ->where('attended', true)
            ->count();

        $absentDays = $attendanceData
            ->where('attended', false)
            ->count();

        $attendancePercentage = $totalDays > 0
            ? round(
                ($attendedDays / $totalDays) * 100,
                2
            )
            : 0;


        /*
    |--------------------------------------------------------------------------
    | Return Data
    |--------------------------------------------------------------------------
    */

        return [

            /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

            'student' => $enrollment->student,

            'enrollment' => $enrollment,


            /*
        |--------------------------------------------------------------------------
        | Class
        |--------------------------------------------------------------------------
        */

            'class' => $enrollment->studentClass,

            'category' => $enrollment
                ->classCategoryFee
                ?->category,

            'teacher' => $enrollment
                ->studentClass
                ?->teacher,

            'grade' => $enrollment
                ->studentClass
                ?->grade,


            /*
        |--------------------------------------------------------------------------
        | Enrollment Information
        |--------------------------------------------------------------------------
        */

            'class_category_fee_id' =>
            $enrollment->class_category_fee_id,

            'enrolled_at' =>
            $enrollment->enrolled_at,

            'left_at' =>
            $enrollment->left_at,


            /*
        |--------------------------------------------------------------------------
        | Daily Attendance
        |--------------------------------------------------------------------------
        */

            'attendance_data' =>
            $attendanceData,


            /*
        |--------------------------------------------------------------------------
        | MONTHLY ATTENDANCE
        |--------------------------------------------------------------------------
        */

            'monthly_attendance' =>
            $monthlyAttendance,


            /*
        |--------------------------------------------------------------------------
        | Overall Summary
        |--------------------------------------------------------------------------
        */

            'total_days' =>
            $totalDays,

            'attended_days' =>
            $attendedDays,

            'absent_days' =>
            $absentDays,

            'attendance_percentage' =>
            $attendancePercentage,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Get Tute Details
    |--------------------------------------------------------------------------
    |
    | Tutes are restricted to:
    | Student
    | + Enrollment
    | + Student Class
    | + Category Fee
    |
    */

    public function getTuteDetails(
        int $studentId,
        int $studentClassId,
        int $enrollmentId
    ): array {
        $enrollment = StudentClassEnrollment::with([
            'student',
            'studentClass.teacher',
            'studentClass.grade',
            'classCategoryFee.category',
        ])
            ->where(
                'id',
                $enrollmentId
            )
            ->where(
                'student_id',
                $studentId
            )
            ->where(
                'student_class_id',
                $studentClassId
            )
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Category Fee ID
        |--------------------------------------------------------------------------
        */

        $classCategoryFeeId =
            $enrollment->class_category_fee_id;

        /*
        |--------------------------------------------------------------------------
        | Get Class + Category-wise Tutes
        |--------------------------------------------------------------------------
        */

        $tutes = StudentTute::query()
            ->where(
                'student_id',
                $studentId
            )

            ->where(
                'student_class_enrollment_id',
                $enrollmentId
            )

            ->whereHas(
                'enrollment',
                function ($query) use (
                    $studentClassId,
                    $classCategoryFeeId
                ) {

                    $query
                        ->where(
                            'student_class_id',
                            $studentClassId
                        )

                        ->where(
                            'class_category_fee_id',
                            $classCategoryFeeId
                        );
                }
            )

            ->with([
                'issuedBy',
            ])

            ->orderByDesc(
                'issued_month'
            )

            ->get();

        /*
        |--------------------------------------------------------------------------
        | Tute Summary
        |--------------------------------------------------------------------------
        */

        $totalTutes =
            $tutes->count();

        $issuedTutes =
            $tutes
            ->where(
                'is_issued',
                true
            )
            ->count();

        $pendingTutes =
            $tutes
            ->where(
                'is_issued',
                false
            )
            ->count();

        return [

            'student' =>
            $enrollment->student,

            'enrollment' =>
            $enrollment,

            'class' =>
            $enrollment->studentClass,

            'category' =>
            $enrollment->classCategoryFee?->category,

            'teacher' =>
            $enrollment->studentClass?->teacher,

            'grade' =>
            $enrollment->studentClass?->grade,

            'class_category_fee_id' =>
            $classCategoryFeeId,

            'enrolled_at' =>
            $enrollment->enrolled_at,

            'tutes' =>
            $tutes,

            'total_tutes' =>
            $totalTutes,

            'issued_tutes' =>
            $issuedTutes,

            'pending_tutes' =>
            $pendingTutes,
        ];
    }
}
