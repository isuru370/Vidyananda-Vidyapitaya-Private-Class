@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Edit Fee Option
            </h4>

            <p class="text-muted mb-0">
                Update the predefined fee option.
            </p>
        </div>

    </div>


    <div class="card">

        <div class="card-body">

            <form
                action="{{ route(
                    'admin.class-category-fee-options.update',
                    $feeOption->id
                ) }}"
                method="POST"
            >

                @csrf

                @method('PUT')

                @include(
                    'admin.class_category_fee_options.partials.form'
                )

            </form>

        </div>

    </div>

</div>

@endsection