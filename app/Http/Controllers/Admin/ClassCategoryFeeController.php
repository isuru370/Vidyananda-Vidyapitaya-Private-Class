<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassCategory;
use App\Models\StudentClass;
use App\Services\ClassCategoryFeeService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;

class ClassCategoryFeeController extends Controller
{
    protected ClassCategoryFeeService $classCategoryFeeService;

    public function __construct(
        ClassCategoryFeeService $classCategoryFeeService
    ) {
        $this->classCategoryFeeService = $classCategoryFeeService;
    }

    /**
     * Index.
     */
    public function index(Request $request)
    {
        $filters = [
            'student_class_id' => $request->student_class_id,
            'class_category_id' => $request->class_category_id,
            'is_active' => $request->filled('is_active')
                ? $request->boolean('is_active')
                : null,
            'per_page' => $request->per_page ?: 10,
        ];

        if (!$request->filled('is_active')) {
            unset($filters['is_active']);
        }

        $fees = $this->classCategoryFeeService
            ->getAll($filters);

        $classes = StudentClass::with([
            'grade',
            'subject',
        ])
            ->where('is_active', true)
            ->orderBy('class_name')
            ->get();

        $categories = ClassCategory::where(
            'is_active',
            true
        )
            ->orderBy('category_name')
            ->get();

        return view(
            'admin.class_category_fees.index',
            compact(
                'fees',
                'classes',
                'categories'
            )
        );
    }

    /**
     * Create.
     */
    public function create(Request $request)
    {
        $selectedClassId =
            $request->student_class_id;

        if ($selectedClassId) {

            $selectedClass = StudentClass::with([
                'grade',
                'subject',
                'teacher',
            ])->findOrFail($selectedClassId);

            if (!$selectedClass->is_active) {

                return redirect()
                    ->route(
                        'admin.student-classes.index'
                    )
                    ->with(
                        'error',
                        'Inactive class එකකට category fee add කරන්න බැහැ.'
                    );
            }
        }

        $classes = StudentClass::with([
            'grade',
            'subject',
            'teacher',
        ])
            ->where('is_active', true)
            ->orderBy('class_name')
            ->get();

        $categories = ClassCategory::where(
            'is_active',
            true
        )
            ->orderBy('category_name')
            ->get();

        return view(
            'admin.class_category_fees.create',
            compact(
                'classes',
                'categories',
                'selectedClassId'
            )
        );
    }

    /**
     * Store.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_class_id' => [
                'required',
                'integer',
                'exists:student_classes,id',
            ],

            'class_category_id' => [
                'required',
                'integer',
                'exists:class_categories,id',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'note' => [
                'nullable',
                'string',
            ],
        ]);

        try {

            $classCategoryFee =
                $this->classCategoryFeeService
                    ->create($validated);

            return redirect()
                ->route(
                    'admin.class-category-fees.index'
                )
                ->with(
                    'success',
                    'Class category fee created successfully.'
                );

        } catch (ValidationException $e) {

            return back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }

    /**
     * Show.
     */
    public function show($id)
    {
        $classCategoryFee =
            $this->classCategoryFeeService
                ->find($id);

        return view(
            'admin.class_category_fees.show',
            compact('classCategoryFee')
        );
    }

    /**
     * Edit.
     */
    public function edit($id)
    {
        $classCategoryFee =
            $this->classCategoryFeeService
                ->find($id);

        if (
            !$classCategoryFee->studentClass ||
            !$classCategoryFee->studentClass->is_active
        ) {
            return redirect()
                ->route(
                    'admin.class-category-fees.index'
                )
                ->with(
                    'error',
                    'Inactive class fee edit කරන්න බැහැ.'
                );
        }

        $classes = StudentClass::where(
            'is_active',
            true
        )
            ->orderBy('class_name')
            ->get();

        $categories = ClassCategory::where(
            'is_active',
            true
        )
            ->orderBy('category_name')
            ->get();

        return view(
            'admin.class_category_fees.edit',
            compact(
                'classCategoryFee',
                'classes',
                'categories'
            )
        );
    }

    /**
     * Update.
     */
    public function update(
        Request $request,
        $id
    ) {
        $validated = $request->validate([
            'student_class_id' => [
                'required',
                'integer',
                'exists:student_classes,id',
            ],

            'class_category_id' => [
                'required',
                'integer',
                'exists:class_categories,id',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'note' => [
                'nullable',
                'string',
            ],
        ]);

        try {

            $this->classCategoryFeeService
                ->update(
                    $id,
                    $validated
                );

            return redirect()
                ->route(
                    'admin.class-category-fees.index'
                )
                ->with(
                    'success',
                    'Class category fee updated successfully.'
                );

        } catch (ValidationException $e) {

            return back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }

    /**
     * Delete.
     */
    public function destroy($id)
    {
        try {

            $this->classCategoryFeeService
                ->delete($id);

            return redirect()
                ->route(
                    'admin.class-category-fees.index'
                )
                ->with(
                    'success',
                    'Class category fee deleted successfully.'
                );

        } catch (ValidationException $e) {

            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (Exception $e) {

            Log::error(
                'Class category fee delete failed',
                [
                    'id' => $id,
                    'error' => $e->getMessage(),
                ]
            );

            return back()->with(
                'error',
                'Class category fee delete failed.'
            );
        }
    }

    /**
     * Toggle active.
     */
    public function toggleActive($id)
    {
        try {

            $this->classCategoryFeeService
                ->toggleActive($id);

            return back()->with(
                'success',
                'Class category fee status updated.'
            );

        } catch (Exception $e) {

            Log::error(
                'Class category fee status update failed',
                [
                    'id' => $id,
                    'error' => $e->getMessage(),
                ]
            );

            return back()->with(
                'error',
                'Unable to update class category fee status.'
            );
        }
    }

    /**
     * Get category fees by class.
     *
     * Used by AJAX / frontend.
     */
    public function byClass(
        StudentClass $studentClass
    ) {
        $fees = $this->classCategoryFeeService
            ->getByClass($studentClass->id);

        return response()->json(
            $fees->map(function ($fee) {

                return [
                    'id' => $fee->id,

                    'category_id' =>
                        $fee->class_category_id,

                    'category_name' =>
                        $fee->category
                            ? $fee->category->category_name
                            : '-',

                    'fee_options' =>
                        $fee->activeFeeOptions
                            ->map(function ($option) {

                                return [
                                    'id' => $option->id,
                                    'label' => $option->label,
                                    'fee' => (float) $option->fee,
                                    'is_default' =>
                                        (bool) $option->is_default,
                                    'note' => $option->note,
                                ];
                            })
                            ->values(),
                ];
            })->values()
        );
    }
}