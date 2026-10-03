@extends('layouts.app')

@section('title', 'Category Students')
@section('page-title', 'Category Students')


@section('content')

<div class="container-fluid">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white border-0 py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-1 fw-bold">
                        {{ $studentClass->class_name }}
                    </h5>

                    <small class="text-muted">

                        Grade:
                        {{ optional($studentClass->grade)->grade_name ?? '-' }}

                        <span class="mx-1">|</span>

                        Subject:
                        {{ optional($studentClass->subject)->subject_name ?? '-' }}

                        <span class="mx-1">|</span>

                        Teacher:
                        {{ optional($studentClass->teacher)->initials ?? '-' }}

                        <span class="mx-1">|</span>

                        Category:
                        {{ $classCategory->category_name }}

                    </small>

                </div>


                <div class="d-flex gap-2">

                    <a href="{{ route(
                        'admin.student-class-enrollments.categoryStudentsPdf',
                        [$studentClass->id, $classCategory->id] + request()->query()
                    ) }}"
                       class="btn btn-danger btn-sm">

                        <i class="bi bi-file-earmark-pdf me-1"></i>

                        PDF

                    </a>


                    <a href="{{ route(
                        'admin.student-class-enrollments.categoryStudentsExcel',
                        [$studentClass->id, $classCategory->id] + request()->query()
                    ) }}"
                       class="btn btn-success btn-sm">

                        <i class="bi bi-file-earmark-excel me-1"></i>

                        Excel

                    </a>


                    <a href="{{ route(
                        'admin.student-class-enrollments.index'
                    ) }}"
                       class="btn btn-outline-secondary btn-sm">

                        <i class="bi bi-arrow-left me-1"></i>

                        Back

                    </a>

                </div>

            </div>

        </div>


        {{-- =====================================================
            BODY
        ====================================================== --}}
        <div class="card-body">

            @if(session('success'))

                <div class="alert alert-success">

                    <i class="bi bi-check-circle me-1"></i>

                    {{ session('success') }}

                </div>

            @endif


            {{-- =================================================
                SEARCH
            ================================================== --}}
            <form method="GET"
                  action="{{ route(
                      'admin.student-class-enrollments.categoryStudents',
                      [$studentClass->id, $classCategory->id]
                  ) }}"
                  class="row g-2 mb-4">

                <div class="col-md-8">

                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search Student ID / QR / Initial Name / Full Name / Mobile..."
                           value="{{ request('search') }}">

                </div>


                <div class="col-md-2">

                    <select name="per_page"
                            class="form-select">

                        <option value="10"
                            {{ request('per_page', 20) == 10 ? 'selected' : '' }}>

                            10

                        </option>

                        <option value="20"
                            {{ request('per_page', 20) == 20 ? 'selected' : '' }}>

                            20

                        </option>

                        <option value="50"
                            {{ request('per_page') == 50 ? 'selected' : '' }}>

                            50

                        </option>

                        <option value="100"
                            {{ request('per_page') == 100 ? 'selected' : '' }}>

                            100

                        </option>

                    </select>

                </div>


                <div class="col-md-1">

                    <button class="btn btn-primary w-100"
                            type="submit">

                        <i class="bi bi-search me-1"></i>

                        Search

                    </button>

                </div>


                <div class="col-md-1">

                    <a href="{{ route(
                        'admin.student-class-enrollments.categoryStudents',
                        [$studentClass->id, $classCategory->id]
                    ) }}"
                       class="btn btn-outline-secondary w-100">

                        Reset

                    </a>

                </div>

            </form>


            {{-- =================================================
                STUDENTS TABLE
            ================================================== --}}
            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Student
                            </th>

                            <th>
                                Contact
                            </th>

                            <th>
                                QR Details
                            </th>

                            <th>
                                Fee Option
                            </th>

                            <th>
                                Fee
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-end">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($enrollments as $key => $enrollment)

                            @php

                                $student = $enrollment->student;

                                /*
                                |--------------------------------------------------------------------------
                                | QR
                                |--------------------------------------------------------------------------
                                */

                                $studentCode = '-';

                                if (
                                    $student &&
                                    $student->permanent_qr_active &&
                                    !empty($student->custom_id) &&
                                    $student->custom_id != '0'
                                ) {

                                    $studentCode = $student->custom_id;

                                    $qrType = 'Permanent QR';

                                    $qrBadge = 'bg-success';

                                } else {

                                    $studentCode =
                                        $student->temporary_qr_code ?? '-';

                                    $qrType = 'Temporary QR';

                                    $qrBadge = 'bg-warning text-dark';

                                }


                                /*
                                |--------------------------------------------------------------------------
                                | Fee Option
                                |--------------------------------------------------------------------------
                                */

                                $feeOption =
                                    $enrollment->classCategoryFeeOption;


                                /*
                                |--------------------------------------------------------------------------
                                | Selected Fee
                                |--------------------------------------------------------------------------
                                */

                                $selectedFee =
                                    $enrollment->final_fee;


                            @endphp


                            <tr>


                                {{-- =================================================
                                    NUMBER
                                ================================================== --}}
                                <td>

                                    {{ $enrollments->firstItem() + $key }}

                                </td>


                                {{-- =================================================
                                    STUDENT
                                ================================================== --}}
                                <td>

                                    <div class="fw-bold">

                                        {{ $student->full_name ?? '-' }}

                                    </div>

                                    <small class="text-muted">

                                        {{ $student->initial_name ?? '-' }}

                                    </small>

                                    <br>

                                    <small class="text-muted">

                                        ID:
                                        {{ $student->custom_id ?? '-' }}

                                    </small>

                                </td>


                                {{-- =================================================
                                    CONTACT
                                ================================================== --}}
                                <td>

                                    <div>

                                        {{ $student->mobile ?? '-' }}

                                    </div>

                                </td>


                                {{-- =================================================
                                    QR
                                ================================================== --}}
                                <td>

                                    <strong>

                                        {{ $studentCode }}

                                    </strong>

                                    <br>

                                    <span class="badge {{ $qrBadge }} mt-1">

                                        {{ $qrType }}

                                    </span>

                                </td>


                                {{-- =================================================
                                    FEE OPTION
                                ================================================== --}}
                                <td>

                                    @if($enrollment->is_free_card)

                                        <span class="badge bg-dark">

                                            Free Card

                                        </span>

                                    @elseif($feeOption)

                                        <span class="badge bg-primary">

                                            {{ $feeOption->label }}

                                        </span>

                                        @if($feeOption->is_default)

                                            <br>

                                            <small class="text-muted">

                                                Default Option

                                            </small>

                                        @endif

                                    @else

                                        <span class="text-muted">

                                            -

                                        </span>

                                    @endif

                                </td>


                                {{-- =================================================
                                    FEE
                                ================================================== --}}
                                <td>

                                    @if($enrollment->is_free_card)

                                        <span class="fw-bold">

                                            Rs. 0.00

                                        </span>

                                    @elseif($feeOption)

                                        <span class="fw-bold">

                                            Rs.
                                            {{ number_format(
                                                (float) $selectedFee,
                                                2
                                            ) }}

                                        </span>

                                        <br>

                                        <small class="text-muted">

                                            Option:
                                            Rs.
                                            {{ number_format(
                                                (float) $feeOption->fee,
                                                2
                                            ) }}

                                        </small>

                                    @else

                                        <span class="text-muted">

                                            -

                                        </span>

                                    @endif

                                </td>


                                {{-- =================================================
                                    STATUS
                                ================================================== --}}
                                <td>

                                    @if($enrollment->is_active)

                                        <span class="badge bg-success">

                                            Active

                                        </span>

                                    @else

                                        <span class="badge bg-secondary">

                                            Inactive

                                        </span>

                                        @if($enrollment->left_at)

                                            <br>

                                            <small class="text-muted">

                                                Left:
                                                {{ $enrollment->left_at->format('Y-m-d') }}

                                            </small>

                                        @endif

                                    @endif

                                </td>


                                {{-- =================================================
                                    ACTIONS
                                ================================================== --}}
                                <td class="text-end">

                                    <div class="d-flex justify-content-end gap-1 flex-wrap">


                                        {{-- Edit --}}
                                        <a href="{{ route(
                                            'admin.student-class-enrollments.edit',
                                            $enrollment
                                        ) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit">

                                            <i class="bi bi-pencil"></i>

                                        </a>


                                        {{-- Toggle --}}
                                        <form method="POST"
                                              action="{{ route(
                                                  'admin.student-class-enrollments.toggleActive',
                                                  $enrollment
                                              ) }}"
                                              onsubmit="return confirm('Change enrollment status?')">

                                            @csrf

                                            @method('PATCH')


                                            @if($enrollment->is_active)

                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        title="Deactivate">

                                                    <i class="bi bi-pause-fill"></i>

                                                </button>

                                            @else

                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-success"
                                                        title="Activate">

                                                    <i class="bi bi-play-fill"></i>

                                                </button>

                                            @endif

                                        </form>

                                    </div>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="8"
                                    class="text-center text-muted py-5">

                                    <i class="bi bi-people fs-1 d-block mb-2"></i>

                                    No students found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =================================================
                PAGINATION
            ================================================== --}}
            <div class="mt-3">

                {{ $enrollments->links() }}

            </div>

        </div>

    </div>

</div>

@endsection