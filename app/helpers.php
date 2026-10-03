<?php

if (!function_exists('hasPermission')) {

    function hasPermission($permission = null)
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        if (!$user->is_active) {
            return false;
        }

        if (!$user->userType) {
            return false;
        }

        if (!$user->userType->is_active) {
            return false;
        }

        $role = strtoupper($user->userType->code);

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        if ($role === 'SUPER_ADMIN') {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if ($role === 'ADMIN') {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        $permissions = [

            'USER' => [

                // Main
                'dashboard',
                'weekly-timetable',

                // Notifications
                'notification.view',
                'notification.create',
                'notification.delete',

                // Management
                'students.index',
                'teachers.index',
                'organizers.index',
                'student-class-management.index',

                // Student Services
                'student-images.index',
                'student-cards.index',

                // Academic
                'student-classes.index',
                'class-schedules.index',
                'student-class-enrollments.index',
                'attendance.index',
                'new-attendance.index',

                // Exams
                'exams.index',

                // Student Finance
                'payments.index',
                'payment-reminder.index',
                'new-payment.index',
                'payments.today-receipt',

                // Institute Finance
                // 'teacher-salaries.index',
                // 'organizer-payments.index',
                'extra-incomes.index',
                'institute-expenses.index',
                // 'institute-income.monthly-report',

                // Receipts
                'receipts.index',

                // Reports
                'daily-report.index',
                'monthly-report.index',
                'teacher-report.index',
                'institute-reports.index',

                // Activities
                // 'activity-logs.index',

                // FCM
                'fcm-tokens.view',
                'fcm-tokens.update',
            ],

            /*
            |--------------------------------------------------------------------------
            | TEACHER
            |--------------------------------------------------------------------------
            */

            'TEACHER' => [

                'dashboard',

                'weekly-timetable',

                'student-classes.index',
                'class-schedules.index',

                'attendance.index',

                'exams.index',

                'student-images.index',
            ],
        ];

        return in_array(
            $permission,
            $permissions[$role] ?? [],
            true
        );
    }
}