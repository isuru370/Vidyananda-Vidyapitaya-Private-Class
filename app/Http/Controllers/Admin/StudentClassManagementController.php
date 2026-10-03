<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentClassEnrollment;
use App\Models\StudentClass;
use App\Models\ClassCategoryFee;
use App\Models\ClassCategoryFeeOption;
use App\Services\StudentClassManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentClassManagementController extends Controller
{
    protected StudentClassManagementService $studentClassManagementService;

    public function __construct(
        StudentClassManagementService $studentClassManagementService
    ) {
        $this->studentClassManagementService = $studentClassManagementService;
    }


    /**
     * Student Class Management Main Page
     */
    public function index()
    {
        return view('admin.student-class-management.index');
    }


    /**
     * Search Student by:
     * - Full Name
     * - Initial Name
     * - Mobile
     * - WhatsApp Mobile
     * - Guardian Mobile
     * - Custom ID
     */
    public function searchStudentClasses(Request $request)
    {
        try {

            $search = trim((string) $request->input('search'));

            /*
        |--------------------------------------------------------------------------
        | No Search
        |--------------------------------------------------------------------------
        */

            if ($request->isMethod('get') && $search === '') {

                return redirect()
                    ->route('admin.student-class-management.index')
                    ->with(
                        'info',
                        'Please enter Student Name, Mobile Number or Custom ID'
                    );
            }


            /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

            $validator = Validator::make(
                ['search' => $search],
                [
                    'search' => 'required|string|max:255',
                ]
            );

            if ($validator->fails()) {

                return redirect()
                    ->route('admin.student-class-management.index')
                    ->withErrors($validator)
                    ->withInput();
            }


            /*
        |--------------------------------------------------------------------------
        | Search Students
        |--------------------------------------------------------------------------
        */

            $students = Student::query()
                ->where(function ($query) use ($search) {

                    $query
                        ->where('full_name', 'LIKE', "%{$search}%")
                        ->orWhere('initial_name', 'LIKE', "%{$search}%")
                        ->orWhere('custom_id', 'LIKE', "%{$search}%")
                        ->orWhere('mobile', 'LIKE', "%{$search}%")
                        ->orWhere('whatsapp_mobile', 'LIKE', "%{$search}%")
                        ->orWhere('guardian_mobile', 'LIKE', "%{$search}%");
                })
                ->with([
                    'enrollments' => function ($query) {

                        $query->with([
                            'studentClass.grade',
                            'studentClass.teacher',
                            'classCategoryFee.category',
                            'classCategoryFeeOption',
                        ]);
                    }
                ])
                ->orderBy('initial_name')
                ->limit(50)
                ->get();


            /*
        |--------------------------------------------------------------------------
        | No Student Found
        |--------------------------------------------------------------------------
        */

            if ($students->isEmpty()) {

                return redirect()
                    ->route('admin.student-class-management.index')
                    ->with(
                        'error',
                        'No student found for: ' . $search
                    )
                    ->withInput();
            }


            /*
        |--------------------------------------------------------------------------
        | One Student Found
        |--------------------------------------------------------------------------
        */

            if ($students->count() === 1) {

                $student = $students->first();

                $classes = $student->enrollments->map(
                    function ($enrollment) {

                        $studentClass = $enrollment->studentClass;
                        $grade = $studentClass
                            ? $studentClass->grade
                            : null;

                        $teacher = $studentClass
                            ? $studentClass->teacher
                            : null;

                        $categoryFee = $enrollment->classCategoryFee;
                        $category = $categoryFee
                            ? $categoryFee->category
                            : null;

                        $feeOption = $enrollment->classCategoryFeeOption;


                        /*
                    |--------------------------------------------------------------------------
                    | Selected Fee
                    |--------------------------------------------------------------------------
                    */

                        $selectedFee = 0;

                        if (!$enrollment->is_free_card && $feeOption) {
                            $selectedFee = (float) $feeOption->fee;
                        }


                        /*
                    |--------------------------------------------------------------------------
                    | Return Enrollment Data
                    |--------------------------------------------------------------------------
                    */

                        return [

                            'enrollment_id' =>
                            $enrollment->id,

                            'class_id' =>
                            $enrollment->student_class_id,

                            'class_category_fee_id' =>
                            $enrollment->class_category_fee_id,

                            'class_category_fee_option_id' =>
                            $enrollment->class_category_fee_option_id,


                            /*
                        | Class Details
                        */

                            'class_name' =>
                            $studentClass
                                ? $studentClass->class_name
                                : 'N/A',

                            'class_type' =>
                            $studentClass
                                ? $studentClass->class_type
                                : 'N/A',

                            'medium' =>
                            $studentClass
                                ? $studentClass->medium
                                : 'N/A',


                            /*
                        | Grade
                        */

                            'grade_name' =>
                            $grade
                                ? $grade->grade_name
                                : 'N/A',


                            /*
                        | Teacher
                        */

                            'teacher_initials' =>
                            $teacher
                                ? $teacher->initials
                                : 'N/A',


                            /*
                        | Category
                        */

                            'category_name' =>
                            $category
                                ? $category->category_name
                                : 'N/A',


                            /*
                        | Fee Option
                        */

                            'fee_option' => $feeOption
                                ? [
                                    'id' =>
                                    $feeOption->id,

                                    'label' =>
                                    $feeOption->label,

                                    'fee' =>
                                    (float) $feeOption->fee,

                                    'is_default' =>
                                    (bool) $feeOption->is_default,

                                    'is_active' =>
                                    (bool) $feeOption->is_active,

                                    'note' =>
                                    $feeOption->note,
                                ]
                                : null,


                            /*
                        | Selected Fee
                        */

                            'fee' =>
                            $selectedFee,

                            'final_fee' =>
                            $selectedFee,


                            /*
                        | Enrollment Status
                        */

                            'is_active' =>
                            (bool) $enrollment->is_active,

                            'is_free_card' =>
                            (bool) $enrollment->is_free_card,


                            /*
                        | Payment
                        */

                            'payment_status' =>
                            $enrollment->payment_status,


                            /*
                        | Dates
                        */

                            'enrolled_at' =>
                            $enrollment->enrolled_at
                                ? $enrollment->enrolled_at->format('Y-m-d')
                                : null,

                            'left_at' =>
                            $enrollment->left_at
                                ? $enrollment->left_at->format('Y-m-d')
                                : null,


                            /*
                        | Note
                        */

                            'note' =>
                            $enrollment->note,
                        ];
                    }
                );


                return view(
                    'admin.student-class-management.show',
                    compact(
                        'student',
                        'classes'
                    )
                );
            }


            /*
        |--------------------------------------------------------------------------
        | Multiple Students Found
        |--------------------------------------------------------------------------
        */

            return view(
                'admin.student-class-management.search-results',
                compact(
                    'students',
                    'search'
                )
            );
        } catch (\Exception $e) {

            return redirect()
                ->route('admin.student-class-management.index')
                ->with(
                    'error',
                    'An error occurred: ' . $e->getMessage()
                )
                ->withInput();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT
    |--------------------------------------------------------------------------
    */

    /**
     * Get Payment Details for a specific
     * Student + Class + Enrollment
     */
    public function payment(
        int $studentId,
        int $studentClassId,
        int $enrollmentId
    ) {
        try {

            $data = $this->studentClassManagementService
                ->getPaymentDetails(
                    $studentId,
                    $studentClassId,
                    $enrollmentId
                );

            return view(
                'admin.student-class-management.payment',
                compact('data')
            );
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unable to load payment details: ' .
                        $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE
    |--------------------------------------------------------------------------
    */

    /**
     * Get Attendance Details for a specific
     * Student + Class + Enrollment
     */
    public function attendance(
        int $studentId,
        int $studentClassId,
        int $enrollmentId
    ) {
        try {

            $data = $this->studentClassManagementService
                ->getAttendanceDetails(
                    $studentId,
                    $studentClassId,
                    $enrollmentId
                );

            return view(
                'admin.student-class-management.attendance',
                compact('data')
            );
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unable to load attendance details: ' .
                        $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | TUTE
    |--------------------------------------------------------------------------
    */

    /**
     * Get Tute Details for a specific
     * Student + Class + Enrollment + Category Fee
     */
    public function tute(
        int $studentId,
        int $studentClassId,
        int $enrollmentId
    ) {
        try {

            $data = $this->studentClassManagementService
                ->getTuteDetails(
                    $studentId,
                    $studentClassId,
                    $enrollmentId
                );

            return view(
                'admin.student-class-management.tute',
                compact('data')
            );
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unable to load tute details: ' .
                        $e->getMessage()
                );
        }
    }


    /**
     * Student එකේ specific class එකක්
     * active / inactive කරනවා
     */
    public function toggleClassStatus(
        Request $request,
        $enrollmentId
    ) {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'is_active' => 'required|boolean',
                    'left_at' => 'nullable|date',
                    'note' => 'nullable|string|max:500',
                ]
            );

            if ($validator->fails()) {

                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }


            $enrollment = StudentClassEnrollment::with([
                'student',
                'studentClass'
            ])->find($enrollmentId);


            if (!$enrollment) {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Class enrollment not found'
                    );
            }


            $studentId = $enrollment->student_id;

            $enrollment->is_active =
                $request->boolean('is_active');


            /*
            |--------------------------------------------------------------------------
            | Deactivate
            |--------------------------------------------------------------------------
            */

            if (!$request->boolean('is_active')) {

                $enrollment->left_at =
                    $request->left_at ?? now();
            } else {

                /*
                |--------------------------------------------------------------------------
                | Activate
                |--------------------------------------------------------------------------
                */

                $enrollment->left_at = null;
            }


            if ($request->has('note')) {

                $enrollment->note =
                    $request->note;
            }


            $enrollment->save();


            $message =
                $request->boolean('is_active')
                ? 'Class activated successfully'
                : 'Class deactivated successfully';


            return redirect()
                ->route(
                    'admin.student-class-management.show',
                    $studentId
                )
                ->with(
                    'success',
                    $message
                );
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'An error occurred while updating class status: ' .
                        $e->getMessage()
                );
        }
    }


    /**
     * Student එකේ specific class එකක් deactivate කරනවා
     */
    public function deactivateClass($enrollmentId)
    {
        $enrollment =
            StudentClassEnrollment::find(
                $enrollmentId
            );


        if (!$enrollment) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Enrollment not found'
                );
        }


        $studentId =
            $enrollment->student_id;


        $enrollment->is_active = false;

        $enrollment->left_at = now();

        $enrollment->save();


        return redirect()
            ->route(
                'admin.student-class-management.show',
                $studentId
            )
            ->with(
                'success',
                'Class deactivated successfully'
            );
    }


    /**
     * Student එකේ specific class එකක් activate කරනවා
     */
    public function activateClass($enrollmentId)
    {
        $enrollment =
            StudentClassEnrollment::find(
                $enrollmentId
            );


        if (!$enrollment) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Enrollment not found'
                );
        }


        $studentId =
            $enrollment->student_id;


        $enrollment->is_active = true;

        $enrollment->left_at = null;

        $enrollment->save();


        return redirect()
            ->route(
                'admin.student-class-management.show',
                $studentId
            )
            ->with(
                'success',
                'Class activated successfully'
            );
    }


    /**
     * Student එකේ සියලු classes
     * එකවර active / inactive කරනවා
     */
    public function toggleAllClassesStatus(
        Request $request,
        $studentId
    ) {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'is_active' => 'required|boolean',
                ]
            );


            if ($validator->fails()) {

                return redirect()
                    ->route(
                        'admin.student-class-management.show',
                        $studentId
                    )
                    ->withErrors($validator);
            }


            $student =
                Student::find($studentId);


            if (!$student) {

                return redirect()
                    ->route(
                        'admin.student-class-management.show',
                        $studentId
                    )
                    ->with(
                        'error',
                        'Student not found'
                    );
            }


            $isActive =
                $request->boolean('is_active');


            $updated =
                StudentClassEnrollment::where(
                    'student_id',
                    $studentId
                )->update([
                    'is_active' =>
                    $isActive,

                    'left_at' =>
                    $isActive
                        ? null
                        : now(),
                ]);


            $message =
                $isActive
                ? 'All classes activated successfully'
                : 'All classes deactivated successfully';


            return redirect()
                ->route(
                    'admin.student-class-management.show',
                    $studentId
                )
                ->with(
                    'success',
                    $message .
                        ' (' .
                        $updated .
                        ' classes updated)'
                );
        } catch (\Exception $e) {

            return redirect()
                ->route(
                    'admin.student-class-management.show',
                    $studentId
                )
                ->with(
                    'error',
                    'An error occurred: ' .
                        $e->getMessage()
                );
        }
    }


    /**
     * Student එකට නව class එකක් assign කරන Form
     */
    public function showAssignClassForm($studentId = null)
    {
        $student = null;

        if ($studentId) {

            $student = Student::find($studentId);
        }

        $classes = StudentClass::query()
            ->where('is_active', true)
            ->with([
                'teacher',
                'subject',
                'grade',
                'categoryFees' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->with([
                            'category',
                            'activeFeeOptions' => function ($optionQuery) {
                                $optionQuery
                                    ->where('is_active', true)
                                    ->orderByDesc('is_default')
                                    ->orderBy('id');
                            },
                        ]);
                },
            ])
            ->orderBy('class_name')
            ->get();

        $students = Student::query()
            ->where('is_active', true)
            ->orderBy('initial_name')
            ->get();

        return view(
            'admin.student-class-management.assign',
            compact(
                'student',
                'classes',
                'students'
            )
        );
    }


    /**
     * Student එකට නව class එකක් assign කරනවා
     */
    public function assignClassToStudent(Request $request)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
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

                    'is_active' => [
                        'boolean',
                    ],

                    'is_free_card' => [
                        'boolean',
                    ],

                    'enrolled_at' => [
                        'nullable',
                        'date',
                    ],

                    'note' => [
                        'nullable',
                        'string',
                        'max:500',
                    ],
                ]
            );

            if ($validator->fails()) {

                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }


            /*
        |--------------------------------------------------------------------------
        | Find Student
        |--------------------------------------------------------------------------
        */

            $student = Student::find($request->student_id);

            if (!$student) {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Student not found.'
                    )
                    ->withInput();
            }

            if (!$student->is_active) {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'This student is inactive.'
                    )
                    ->withInput();
            }


            /*
        |--------------------------------------------------------------------------
        | Find Student Class
        |--------------------------------------------------------------------------
        */

            $studentClass = StudentClass::find(
                $request->student_class_id
            );

            if (!$studentClass) {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Student class not found.'
                    )
                    ->withInput();
            }

            if (!$studentClass->is_active) {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'This class is inactive.'
                    )
                    ->withInput();
            }


            /*
        |--------------------------------------------------------------------------
        | Validate Class Category Fee
        |--------------------------------------------------------------------------
        */

            $classCategoryFee = ClassCategoryFee::query()
                ->where('id', $request->class_category_fee_id)
                ->where(
                    'student_class_id',
                    $request->student_class_id
                )
                ->where('is_active', true)
                ->first();

            if (!$classCategoryFee) {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Invalid or inactive class category fee for this class.'
                    )
                    ->withInput();
            }


            /*
        |--------------------------------------------------------------------------
        | Validate Fee Option
        |--------------------------------------------------------------------------
        */

            $feeOption = ClassCategoryFeeOption::query()
                ->where('id', $request->class_category_fee_option_id)
                ->where(
                    'class_category_fee_id',
                    $classCategoryFee->id
                )
                ->where('is_active', true)
                ->first();

            if (!$feeOption) {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Invalid or inactive fee option for this category.'
                    )
                    ->withInput();
            }


            /*
        |--------------------------------------------------------------------------
        | Check Existing Enrollment
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Same student + same class + same category fee
        | cannot have another active enrollment.
        |
        | But the student CAN enroll in:
        |
        | Theory + Revision + Paper
        |
        */

            $existing = StudentClassEnrollment::query()
                ->where(
                    'student_id',
                    $request->student_id
                )
                ->where(
                    'student_class_id',
                    $request->student_class_id
                )
                ->where(
                    'class_category_fee_id',
                    $request->class_category_fee_id
                )
                ->where('is_active', true)
                ->first();

            if ($existing) {

                /*
            |--------------------------------------------------------------------------
            | If same category already exists
            |--------------------------------------------------------------------------
            */

                if (
                    (int) $existing->class_category_fee_option_id ===
                    (int) $request->class_category_fee_option_id
                ) {

                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'This student is already enrolled in this category with the selected fee option.'
                        )
                        ->withInput();
                }


                /*
            |--------------------------------------------------------------------------
            | Different Fee Option
            |--------------------------------------------------------------------------
            |
            | Existing category enrollment exists.
            | Change selected fee option instead of creating
            | another enrollment because DB unique constraint
            | allows only one active enrollment per category.
            |
            */

                $existing->update([
                    'class_category_fee_option_id' =>
                    $request->class_category_fee_option_id,

                    'is_active' =>
                    $request->boolean(
                        'is_active',
                        true
                    ),

                    'is_free_card' =>
                    $request->boolean(
                        'is_free_card',
                        false
                    ),

                    'enrolled_at' =>
                    $request->enrolled_at
                        ? $request->enrolled_at
                        : $existing->enrolled_at,

                    'note' =>
                    $request->note,
                ]);

                return redirect()
                    ->route(
                        'admin.student-class-management.show',
                        $request->student_id
                    )
                    ->with(
                        'success',
                        'Student class fee option updated successfully.'
                    );
            }


            /*
        |--------------------------------------------------------------------------
        | Create Enrollment
        |--------------------------------------------------------------------------
        */

            StudentClassEnrollment::create([

                'student_id' =>
                $request->student_id,

                'student_class_id' =>
                $request->student_class_id,

                'class_category_fee_id' =>
                $request->class_category_fee_id,

                'class_category_fee_option_id' =>
                $request->class_category_fee_option_id,

                'is_active' =>
                $request->boolean(
                    'is_active',
                    true
                ),

                'is_free_card' =>
                $request->boolean(
                    'is_free_card',
                    false
                ),

                'enrolled_at' =>
                $request->enrolled_at
                    ? $request->enrolled_at
                    : now()->toDateString(),

                'note' =>
                $request->note,
            ]);


            /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

            return redirect()
                ->route(
                    'admin.student-class-management.show',
                    $request->student_id
                )
                ->with(
                    'success',
                    'Class assigned to student successfully.'
                );
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'An error occurred: ' .
                        $e->getMessage()
                )
                ->withInput();
        }
    }


    /**
     * Student එකගේ classes display කරනවා
     */
    /**
     * Student එකගේ classes display කරනවා
     */
    public function showStudentClasses($studentId)
    {
        try {

            $student = Student::query()
                ->with([
                    'enrollments' => function ($query) {

                        $query->with([
                            'studentClass.grade',
                            'studentClass.teacher',
                            'classCategoryFee.category',
                            'classCategoryFeeOption',
                        ]);
                    }
                ])
                ->find($studentId);


            if (!$student) {

                return redirect()
                    ->route(
                        'admin.student-class-management.index'
                    )
                    ->with(
                        'error',
                        'Student not found'
                    );
            }


            $classes = $student->enrollments->map(
                function ($enrollment) {

                    $studentClass =
                        $enrollment->studentClass;

                    $grade =
                        $studentClass
                        ? $studentClass->grade
                        : null;

                    $teacher =
                        $studentClass
                        ? $studentClass->teacher
                        : null;

                    $categoryFee =
                        $enrollment->classCategoryFee;

                    $category =
                        $categoryFee
                        ? $categoryFee->category
                        : null;

                    $feeOption =
                        $enrollment->classCategoryFeeOption;


                    /*
                |--------------------------------------------------------------------------
                | Selected Fee
                |--------------------------------------------------------------------------
                */

                    $selectedFee = 0;

                    if (
                        !$enrollment->is_free_card &&
                        $feeOption
                    ) {

                        $selectedFee =
                            (float) $feeOption->fee;
                    }


                    /*
                |--------------------------------------------------------------------------
                | Return Enrollment Data
                |--------------------------------------------------------------------------
                */

                    return [

                        /*
                    |--------------------------------------------------------------------------
                    | Enrollment
                    |--------------------------------------------------------------------------
                    */

                        'enrollment_id' =>
                        $enrollment->id,

                        'class_id' =>
                        $enrollment->student_class_id,

                        'class_category_fee_id' =>
                        $enrollment->class_category_fee_id,

                        'class_category_fee_option_id' =>
                        $enrollment->class_category_fee_option_id,


                        /*
                    |--------------------------------------------------------------------------
                    | Class Details
                    |--------------------------------------------------------------------------
                    */

                        'class_name' =>
                        $studentClass
                            ? $studentClass->class_name
                            : 'N/A',

                        'class_type' =>
                        $studentClass
                            ? $studentClass->class_type
                            : 'N/A',

                        'medium' =>
                        $studentClass
                            ? $studentClass->medium
                            : 'N/A',


                        /*
                    |--------------------------------------------------------------------------
                    | Grade
                    |--------------------------------------------------------------------------
                    */

                        'grade_name' =>
                        $grade
                            ? $grade->grade_name
                            : 'N/A',


                        /*
                    |--------------------------------------------------------------------------
                    | Teacher
                    |--------------------------------------------------------------------------
                    */

                        'teacher_initials' =>
                        $teacher
                            ? $teacher->initials
                            : 'N/A',


                        /*
                    |--------------------------------------------------------------------------
                    | Category
                    |--------------------------------------------------------------------------
                    */

                        'category_name' =>
                        $category
                            ? $category->category_name
                            : 'N/A',


                        /*
                    |--------------------------------------------------------------------------
                    | Fee Option
                    |--------------------------------------------------------------------------
                    */

                        'fee_option' => $feeOption
                            ? [

                                'id' =>
                                $feeOption->id,

                                'label' =>
                                $feeOption->label,

                                'fee' =>
                                (float) $feeOption->fee,

                                'is_default' =>
                                (bool) $feeOption->is_default,

                                'is_active' =>
                                (bool) $feeOption->is_active,

                                'note' =>
                                $feeOption->note,

                            ]
                            : null,


                        /*
                    |--------------------------------------------------------------------------
                    | Fee
                    |--------------------------------------------------------------------------
                    */

                        'fee' =>
                        $selectedFee,

                        'final_fee' =>
                        $selectedFee,


                        /*
                    |--------------------------------------------------------------------------
                    | Enrollment Status
                    |--------------------------------------------------------------------------
                    */

                        'is_active' =>
                        (bool) $enrollment->is_active,

                        'is_free_card' =>
                        (bool) $enrollment->is_free_card,


                        /*
                    |--------------------------------------------------------------------------
                    | Payment
                    |--------------------------------------------------------------------------
                    */

                        'payment_status' =>
                        $enrollment->payment_status,


                        /*
                    |--------------------------------------------------------------------------
                    | Dates
                    |--------------------------------------------------------------------------
                    */

                        'enrolled_at' =>
                        $enrollment->enrolled_at
                            ? $enrollment->enrolled_at->format('Y-m-d')
                            : null,

                        'left_at' =>
                        $enrollment->left_at
                            ? $enrollment->left_at->format('Y-m-d')
                            : null,


                        /*
                    |--------------------------------------------------------------------------
                    | Note
                    |--------------------------------------------------------------------------
                    */

                        'note' =>
                        $enrollment->note,
                    ];
                }
            );


            return view(
                'admin.student-class-management.show',
                compact(
                    'student',
                    'classes'
                )
            );
        } catch (\Exception $e) {

            return redirect()
                ->route(
                    'admin.student-class-management.index'
                )
                ->with(
                    'error',
                    'Unable to load student classes: ' .
                        $e->getMessage()
                );
        }
    }


    /**
     * Get category fees for a class (AJAX)
     */
    public function getCategoryFees($classId)
    {
        try {

            $fees = ClassCategoryFee::query()
                ->where('student_class_id', $classId)
                ->where('is_active', true)
                ->with([
                    'category',
                    'activeFeeOptions' => function ($query) {
                        $query
                            ->where('is_active', true)
                            ->orderByDesc('is_default')
                            ->orderBy('id');
                    },
                ])
                ->get()
                ->map(function ($fee) {

                    return [

                        'id' =>
                        $fee->id,

                        'category_id' =>
                        $fee->class_category_id,

                        'category_name' =>
                        $fee->category
                            ? $fee->category->category_name
                            : 'N/A',

                        'fee_options' =>
                        $fee->activeFeeOptions
                            ->map(function ($option) {

                                return [

                                    'id' =>
                                    $option->id,

                                    'label' =>
                                    $option->label,

                                    'fee' =>
                                    (float) $option->fee,

                                    'is_default' =>
                                    (bool) $option->is_default,

                                    'is_active' =>
                                    (bool) $option->is_active,

                                    'note' =>
                                    $option->note,

                                ];
                            })
                            ->values()
                            ->toArray(),
                    ];
                })
                ->values()
                ->toArray();


            return response()->json([
                'success' => true,
                'data' => $fees,
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error loading category fees',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
