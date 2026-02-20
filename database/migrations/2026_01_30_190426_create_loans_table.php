<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->morphs('loanable');

            $table->foreignId('student_id')
                ->nullable()
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('professional_id')
                ->nullable()
                ->constrained('professionals')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->dateTime('loan_date');
            $table->dateTime('due_date');
            $table->dateTime('return_date')->nullable();
            $table->string('status')->default('active');
            $table->text('observation')->nullable();
            $table->timestamps();
        });

        Schema::create('waitlists', function (Blueprint $table) {
            $table->id();

            $table->morphs('waitlistable');

            $table->foreignId('student_id')
                ->nullable()
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('professional_id')
                ->nullable()
                ->constrained('professionals')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('requested_at')->useCurrent();
            $table->string('status')->default('waiting');
            $table->text('observation')->nullable();

            $table->timestamps();

            $table->unique(
                ['waitlistable_id', 'waitlistable_type', 'student_id', 'professional_id'],
                'unique_waitlist_entry'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlists');
        Schema::dropIfExists('loans');
    }
};
