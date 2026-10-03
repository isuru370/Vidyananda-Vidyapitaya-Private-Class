<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\StudentIdCard;
use App\Models\StudentCard;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // -------------------------------------------------
        // Date
        // -------------------------------------------------

        $today = Carbon::today();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // -------------------------------------------------
        // Basic Statistics
        // -------------------------------------------------

        $studentsCount = Student::count();

        $teachersCount = Teacher::count();

        $classesCount = StudentClass::count();

        // -------------------------------------------------
        // Today's Income
        // -------------------------------------------------

        $todayIncome = Payment::whereDate('paid_at', $today)
            ->where('status', 'completed')
            ->sum('amount');

        // -------------------------------------------------
        // Current Month Income
        // -------------------------------------------------

        $monthlyIncome = Payment::whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->where('status', 'completed')
            ->sum('amount');

        // -------------------------------------------------
        // Available Student Cards
        // -------------------------------------------------
        //
        // Count only cards which are available
        // for assigning to students.
        //

        $availableStudentCardCount = StudentCard::where(
            'status',
            'available'
        )->count();

        // Show warning if available cards are less than 10

        $showStudentCardWarning = $availableStudentCardCount < 10;

        // -------------------------------------------------
        // Incomplete Student Registrations
        // -------------------------------------------------

        $incompleteRegistrations = Student::where(function ($query) {
            $query->whereNull('full_name')
                ->orWhere('full_name', '')
                ->orWhereNull('mobile')
                ->orWhere('mobile', '')
                ->orWhereNull('address1')
                ->orWhere('address1', '');
        })
            ->select('id', 'custom_id', 'initial_name', 'guardian_mobile')
            ->latest()
            ->take(5)
            ->get();

        $incompleteRegistrationCount = Student::where(function ($query) {
            $query->whereNull('full_name')
                ->orWhere('full_name', '')
                ->orWhereNull('mobile')
                ->orWhere('mobile', '')
                ->orWhereNull('address1')
                ->orWhere('address1', '');
        })
            ->count();

        // -------------------------------------------------
        // Latest Students
        // -------------------------------------------------

        $latestStudents = Student::latest()
            ->take(5)
            ->get([
                'id',
                'custom_id',
                'initial_name',
                'guardian_mobile',
                'created_at',
            ]);

        // -------------------------------------------------
        // Expiring Students
        // -------------------------------------------------

        $expiringStudents = Student::select(
            'id',
            'temporary_qr_code',
            'initial_name',
            'guardian_mobile',
            'temporary_qr_code_expire_date'
        )
            ->where('permanent_qr_active', 0)
            ->whereNotNull('temporary_qr_code_expire_date')
            ->whereBetween('temporary_qr_code_expire_date', [
                Carbon::today(),
                Carbon::today()->addDays(10),
            ])
            ->orderBy('temporary_qr_code_expire_date')
            ->get();

        $expiringStudentsCount = $expiringStudents->count();

        // -------------------------------------------------
        // Return Dashboard View
        // -------------------------------------------------

        return view('admin.dashboard.index', compact(
            // Basic Statistics
            'studentsCount',
            'teachersCount',
            'classesCount',

            // Income
            'todayIncome',
            'monthlyIncome',

            // Student Cards
            'availableStudentCardCount',
            'showStudentCardWarning',

            // Incomplete Registrations
            'incompleteRegistrationCount',
            'incompleteRegistrations',

            // Latest Students
            'latestStudents',

            // Expiring Students
            'expiringStudents',
            'expiringStudentsCount'
        ));
    }
}
