<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentClassEnrollmentsTable extends Migration
{
    public function up()
    {
        Schema::create('student_class_enrollments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('student_class_id')
                ->constrained('student_classes')
                ->cascadeOnDelete();

            $table->foreignId('class_category_fee_id')
                ->constrained('class_category_fees')
                ->restrictOnDelete();

            $table->foreignId('class_category_fee_option_id')
                ->constrained('class_category_fee_options')
                ->restrictOnDelete();

            $table->boolean('is_active')
                ->default(true);

            $table->boolean('is_free_card')
                ->default(false);

            $table->date('enrolled_at')
                ->nullable();

            $table->date('left_at')
                ->nullable();

            $table->text('note')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Unique Enrollment
            |--------------------------------------------------------------------------
            */

            $table->unique(
                [
                    'student_id',
                    'student_class_id',
                    'class_category_fee_id',
                    'class_category_fee_option_id',
                ],
                'sce_student_class_fee_unique'
            );

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(
                ['student_id', 'is_active'],
                'sce_student_active_idx'
            );

            $table->index(
                ['student_class_id', 'is_active'],
                'sce_class_active_idx'
            );

            $table->index(
                ['class_category_fee_id', 'is_active'],
                'sce_category_fee_active_idx'
            );

            $table->index(
                ['class_category_fee_option_id', 'is_active'],
                'sce_fee_option_active_idx'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_class_enrollments');
    }
}
