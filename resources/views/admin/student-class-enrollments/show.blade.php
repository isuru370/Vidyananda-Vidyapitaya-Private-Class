@extends('layouts.app')

@section('title', 'Enrollment Details')
@section('page-title', 'Enrollment Details')


@section('content')

<div class="container-fluid">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-bold mb-1">
                Enrollment Details
            </h4>

            <p class="text-muted mb-0">

                {{ optional($studentClassEnrollment->student)->custom_id ?? '-' }}

                -

                {{ optional($studentClassEnrollment->student)->initial_name ?? '-' }}

            </p>

        </div>


        <div class="d-flex gap-2">

            <a href="{{ route(
                'admin.student-class-enrollments.edit',
                $studentClassEnrollment
            ) }}"
               class="btn btn-primary">

                <i class="bi bi-pencil-square me-1"></i>

                Edit

            </a>


            <a href="{{ route(
                'admin.student-class-enrollments.index'
            ) }}"
               class="btn btn-secondary">

                <i class="bi bi-arrow-left me-1"></i>

                Back

            </a>

        </div>

    </div>


    {{-- =========================================================
        STUDENT / CLASS INFORMATION
    ========================================================== --}}
    <div class="row g-4">


        {{-- =====================================================
            STUDENT INFORMATION
        ====================================================== --}}
        <div class="col-lg-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white border-0 pt-4 px-4">

                    <h5 class="fw-bold mb-0">

                        <i class="bi bi-person-circle text-primary me-2"></i>

                        Student Information

                    </h5>

                </div>


                <div class="card-body px-4">

                    <div class="info-row">

                        <span class="info-label">
                            Student ID
                        </span>

                        <span class="info-value">

                            {{ optional($studentClassEnrollment->student)->custom_id ?? '-' }}

                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Name
                        </span>

                        <span class="info-value">

                            {{ optional($studentClassEnrollment->student)->full_name
                                ?? optional($studentClassEnrollment->student)->initial_name
                                ?? '-' }}

                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Enrollment Status
                        </span>

                        <span class="info-value">

                            @if($studentClassEnrollment->is_active)

                                <span class="badge bg-success">
                                    Active
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    Inactive
                                </span>

                            @endif

                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Free Card
                        </span>

                        <span class="info-value">

                            @if($studentClassEnrollment->is_free_card)

                                <span class="badge bg-success">
                                    Yes
                                </span>

                            @else

                                <span class="badge bg-light text-dark border">
                                    No
                                </span>

                            @endif

                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            CLASS INFORMATION
        ====================================================== --}}
        <div class="col-lg-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white border-0 pt-4 px-4">

                    <h5 class="fw-bold mb-0">

                        <i class="bi bi-journal-bookmark-fill text-primary me-2"></i>

                        Class Information

                    </h5>

                </div>


                <div class="card-body px-4">

                    <div class="info-row">

                        <span class="info-label">
                            Class
                        </span>

                        <span class="info-value">

                            {{ optional($studentClassEnrollment->studentClass)->class_name ?? '-' }}

                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Grade
                        </span>

                        <span class="info-value">

                            {{ optional(
                                optional($studentClassEnrollment->studentClass)->grade
                            )->grade_name ?? '-' }}

                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Subject
                        </span>

                        <span class="info-value">

                            {{ optional(
                                optional($studentClassEnrollment->studentClass)->subject
                            )->subject_name ?? '-' }}

                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Teacher
                        </span>

                        <span class="info-value">

                            {{ optional(
                                optional($studentClassEnrollment->studentClass)->teacher
                            )->initials ?? '-' }}

                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            FEE INFORMATION
        ====================================================== --}}
        <div class="col-lg-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white border-0 pt-4 px-4">

                    <h5 class="fw-bold mb-0">

                        <i class="bi bi-cash-stack text-success me-2"></i>

                        Fee Information

                    </h5>

                </div>


                <div class="card-body px-4">


                    {{-- Category --}}
                    <div class="info-row">

                        <span class="info-label">
                            Category
                        </span>

                        <span class="info-value">

                            {{ optional(
                                optional($studentClassEnrollment->classCategoryFee)->category
                            )->category_name ?? '-' }}

                        </span>

                    </div>


                    {{-- Fee Option --}}
                    <div class="info-row">

                        <span class="info-label">
                            Fee Option
                        </span>

                        <span class="info-value">

                            @if($studentClassEnrollment->classCategoryFeeOption)

                                <span class="badge bg-primary">

                                    {{ $studentClassEnrollment->classCategoryFeeOption->label }}

                                </span>

                            @else

                                <span class="text-muted">
                                    -
                                </span>

                            @endif

                        </span>

                    </div>


                    {{-- Fee Option Amount --}}
                    <div class="info-row">

                        <span class="info-label">
                            Fee
                        </span>

                        <span class="info-value fw-bold">

                            Rs.
                            {{ number_format(
                                (float) $studentClassEnrollment->final_fee,
                                2
                            ) }}

                        </span>

                    </div>


                    {{-- Free Card --}}
                    @if($studentClassEnrollment->is_free_card)

                        <div class="alert alert-success mt-3 mb-0">

                            <i class="bi bi-check-circle-fill me-1"></i>

                            This student has a free card.

                            <strong>
                                No fee is required.
                            </strong>

                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- =====================================================
            PAYMENT INFORMATION
        ====================================================== --}}
        <div class="col-lg-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white border-0 pt-4 px-4">

                    <h5 class="fw-bold mb-0">

                        <i class="bi bi-wallet2 text-warning me-2"></i>

                        Payment Information

                    </h5>

                </div>


                <div class="card-body px-4">


                    {{-- Paid --}}
                    <div class="info-row">

                        <span class="info-label">
                            Paid Amount
                        </span>

                        <span class="info-value text-success fw-bold">

                            Rs.
                            {{ number_format(
                                (float) $studentClassEnrollment->paid_amount,
                                2
                            ) }}

                        </span>

                    </div>


                    {{-- Balance --}}
                    <div class="info-row">

                        <span class="info-label">
                            Balance
                        </span>

                        <span class="info-value text-danger fw-bold">

                            Rs.
                            {{ number_format(
                                (float) $studentClassEnrollment->balance,
                                2
                            ) }}

                        </span>

                    </div>


                    {{-- Payment Status --}}
                    <div class="info-row">

                        <span class="info-label">
                            Payment Status
                        </span>

                        <span class="info-value">

                            @if($studentClassEnrollment->payment_status === 'paid')

                                <span class="badge bg-success">
                                    Paid
                                </span>

                            @elseif($studentClassEnrollment->payment_status === 'partial')

                                <span class="badge bg-warning text-dark">
                                    Partial
                                </span>

                            @elseif($studentClassEnrollment->payment_status === 'unpaid')

                                <span class="badge bg-danger">
                                    Unpaid
                                </span>

                            @else

                                <span class="badge bg-secondary">

                                    {{ ucfirst(
                                        $studentClassEnrollment->payment_status
                                    ) }}

                                </span>

                            @endif

                        </span>

                    </div>


                </div>

            </div>

        </div>


        {{-- =====================================================
            ENROLLMENT INFORMATION
        ====================================================== --}}
        <div class="col-12">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-0 pt-4 px-4">

                    <h5 class="fw-bold mb-0">

                        <i class="bi bi-calendar-check text-primary me-2"></i>

                        Enrollment Information

                    </h5>

                </div>


                <div class="card-body px-4">


                    <div class="row">


                        {{-- Enrolled At --}}
                        <div class="col-md-4">

                            <div class="info-row">

                                <span class="info-label">
                                    Enrolled At
                                </span>

                                <span class="info-value">

                                    @if($studentClassEnrollment->enrolled_at)

                                        {{ $studentClassEnrollment->enrolled_at->format('Y-m-d') }}

                                    @else

                                        -

                                    @endif

                                </span>

                            </div>

                        </div>


                        {{-- Left At --}}
                        <div class="col-md-4">

                            <div class="info-row">

                                <span class="info-label">
                                    Left At
                                </span>

                                <span class="info-value">

                                    @if($studentClassEnrollment->left_at)

                                        {{ $studentClassEnrollment->left_at->format('Y-m-d') }}

                                    @else

                                        -

                                    @endif

                                </span>

                            </div>

                        </div>


                        {{-- Created At --}}
                        <div class="col-md-4">

                            <div class="info-row">

                                <span class="info-label">
                                    Created At
                                </span>

                                <span class="info-value">

                                    @if($studentClassEnrollment->created_at)

                                        {{ $studentClassEnrollment->created_at->format('Y-m-d H:i') }}

                                    @else

                                        -

                                    @endif

                                </span>

                            </div>

                        </div>


                        {{-- Note --}}
                        <div class="col-12 mt-3">

                            <div class="info-row align-items-start">

                                <span class="info-label">
                                    Note
                                </span>

                                <span class="info-value">

                                    {{ $studentClassEnrollment->note ?? '-' }}

                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection



{{-- =============================================================
    STYLES
============================================================= --}}
@push('styles')

<style>

    .card {

        border-radius: 18px;

    }


    .card-header {

        border-radius: 18px 18px 0 0 !important;

    }


    .info-row {

        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 1rem;

        padding: .85rem 0;

        border-bottom: 1px solid #f1f5f9;

    }


    .info-row:last-child {

        border-bottom: none;

    }


    .info-label {

        color: #64748b;

        font-size: .9rem;

        font-weight: 500;

    }


    .info-value {

        color: #0f172a;

        font-size: .95rem;

        font-weight: 600;

        text-align: right;

    }


    .badge {

        border-radius: 8px;

        padding: .45rem .7rem;

        font-size: .75rem;

    }


    @media(max-width:768px) {

        .info-row {

            flex-direction: column;

            align-items: flex-start;

        }


        .info-value {

            text-align: left;

        }

    }

</style>

@endpush