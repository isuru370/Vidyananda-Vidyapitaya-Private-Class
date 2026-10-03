@extends('layouts.app')

@section('title', 'Create Enrollment')
@section('page-title', 'Create Enrollment')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h4 class="fw-bold mb-1">Create Student Enrollment</h4>
        <p class="text-muted mb-0">
            Enroll a student into a class category.
        </p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form
                action="{{ route('admin.student-class-enrollments.store') }}"
                method="POST"
            >

                @csrf

                @include(
                    'admin.student-class-enrollments.partials.form',
                    [
                        'buttonText' => 'Create Enrollment'
                    ]
                )

            </form>

        </div>
    </div>

</div>

@endsection