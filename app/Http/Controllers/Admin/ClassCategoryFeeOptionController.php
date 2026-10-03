<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassCategoryFee;
use App\Services\ClassCategoryFeeOptionService;
use Illuminate\Http\Request;

class ClassCategoryFeeOptionController extends Controller
{
    protected $classCategoryFeeOptionService;

    public function __construct(
        ClassCategoryFeeOptionService $classCategoryFeeOptionService
    ) {
        $this->classCategoryFeeOptionService =
            $classCategoryFeeOptionService;
    }

    /**
     * Display all fee options for a class category fee.
     */
    public function index($classCategoryFeeId)
    {
        $classCategoryFee = ClassCategoryFee::with([
            'category',
            'studentClass',
        ])->findOrFail($classCategoryFeeId);

        $feeOptions = $this->classCategoryFeeOptionService
            ->getOptionsByCategoryFee(
                $classCategoryFeeId
            );

        return view(
            'admin.class_category_fee_options.index',
            compact(
                'classCategoryFee',
                'feeOptions'
            )
        );
    }

    /**
     * Show create form.
     */
    public function create($classCategoryFeeId)
    {
        $classCategoryFee = ClassCategoryFee::with([
            'category',
            'studentClass',
        ])->findOrFail($classCategoryFeeId);

        return view(
            'admin.class_category_fee_options.create',
            compact('classCategoryFee')
        );
    }

    /**
     * Store a new fee option.
     */
    public function store(
        Request $request,
        $classCategoryFeeId
    ) {
        $validated = $request->validate([
            'label' => [
                'required',
                'string',
                'max:150',
            ],

            'fee' => [
                'required',
                'numeric',
                'min:0',
            ],

            'is_default' => [
                'nullable',
                'boolean',
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

        $this->classCategoryFeeOptionService->create([
            'class_category_fee_id' => $classCategoryFeeId,
            'label' => $validated['label'],
            'fee' => $validated['fee'],
            'is_default' => isset($validated['is_default'])
                ? (bool) $validated['is_default']
                : false,
            'is_active' => isset($validated['is_active'])
                ? (bool) $validated['is_active']
                : true,
            'note' => isset($validated['note'])
                ? $validated['note']
                : null,
        ]);

        return redirect()
            ->route(
                'admin.class-category-fee-options.index',
                $classCategoryFeeId
            )
            ->with(
                'success',
                'Fee option created successfully.'
            );
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $feeOption = $this->classCategoryFeeOptionService
            ->find($id);

        $classCategoryFee = $feeOption->classCategoryFee;

        return view(
            'admin.class_category_fee_options.edit',
            compact(
                'feeOption',
                'classCategoryFee'
            )
        );
    }

    /**
     * Update fee option.
     */
    public function update(
        Request $request,
        $id
    ) {
        $validated = $request->validate([
            'label' => [
                'required',
                'string',
                'max:150',
            ],

            'fee' => [
                'required',
                'numeric',
                'min:0',
            ],

            'is_default' => [
                'nullable',
                'boolean',
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

        $feeOption = $this->classCategoryFeeOptionService
            ->find($id);

        $classCategoryFeeId =
            $feeOption->class_category_fee_id;

        $this->classCategoryFeeOptionService->update(
            $id,
            [
                'label' => $validated['label'],
                'fee' => $validated['fee'],
                'is_default' => isset($validated['is_default'])
                    ? (bool) $validated['is_default']
                    : false,
                'is_active' => isset($validated['is_active'])
                    ? (bool) $validated['is_active']
                    : true,
                'note' => isset($validated['note'])
                    ? $validated['note']
                    : null,
            ]
        );

        return redirect()
            ->route(
                'admin.class-category-fee-options.index',
                $classCategoryFeeId
            )
            ->with(
                'success',
                'Fee option updated successfully.'
            );
    }

    /**
     * Delete fee option.
     */
    public function destroy($id)
    {
        $feeOption = $this->classCategoryFeeOptionService
            ->find($id);

        $classCategoryFeeId =
            $feeOption->class_category_fee_id;

        $this->classCategoryFeeOptionService->delete($id);

        return redirect()
            ->route(
                'admin.class-category-fee-options.index',
                $classCategoryFeeId
            )
            ->with(
                'success',
                'Fee option deleted successfully.'
            );
    }

    /**
     * Restore deleted fee option.
     */
    public function restore($id)
    {
        $feeOption = $this->classCategoryFeeOptionService
            ->restore($id);

        return redirect()
            ->route(
                'admin.class-category-fee-options.index',
                $feeOption->class_category_fee_id
            )
            ->with(
                'success',
                'Fee option restored successfully.'
            );
    }

    /**
     * Set fee option as default.
     */
    public function setDefault($id)
    {
        $feeOption = $this->classCategoryFeeOptionService
            ->setDefault($id);

        return redirect()
            ->route(
                'admin.class-category-fee-options.index',
                $feeOption->class_category_fee_id
            )
            ->with(
                'success',
                'Default fee option updated successfully.'
            );
    }

    /**
     * Activate fee option.
     */
    public function activate($id)
    {
        $feeOption = $this->classCategoryFeeOptionService
            ->activate($id);

        return redirect()
            ->route(
                'admin.class-category-fee-options.index',
                $feeOption->class_category_fee_id
            )
            ->with(
                'success',
                'Fee option activated successfully.'
            );
    }

    /**
     * Deactivate fee option.
     */
    public function deactivate($id)
    {
        $feeOption = $this->classCategoryFeeOptionService
            ->deactivate($id);

        return redirect()
            ->route(
                'admin.class-category-fee-options.index',
                $feeOption->class_category_fee_id
            )
            ->with(
                'success',
                'Fee option deactivated successfully.'
            );
    }
}