<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('salary_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
        $table->string('upload_logo')->nullable();
        $table->string('upload_banner')->nullable();
            $table->string('title')->notNullable();
            $table->string('slug')->nullable()->unique();
            // $table->enum('type',['Fulltime','Parttime','Remote'])->default('Fulltime');
            $table->string('categories');
            $table->foreignId('job_category_id')->nullable()->constrained('job_categories')->nullOnDelete();
            $table->date('deadline')->notNullable();
            $table->string('url', 2048)->notNullable();
            $table->string('email', 255)->notNullable();
            $table->string('phone_no', 30);
            $table->enum('salary_type',['Hour','Day','Week', 'Month',  'Quarter'])->notNullable();
            $table->foreignId('salary_type_id')->nullable()->constrained('salary_types')->nullOnDelete();
            $table->decimal('min', 10, 2)->notNullable();
            $table->decimal('max', 10, 2)->notNullable();
            $table->text('description')->notNullable();
            $table->text('requirements')->nullable();
            $table->text('required_skills')->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('job_location', 255)->nullable();

            $table->enum('status',['open','in_progress','completed','closed'])->default('open');
            $table->boolean('closed_by_employer')->default(false);

            // $table->foreignId('accepted_proposal_id')->nullable()->constrained('proposals')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('salary_types');
        Schema::dropIfExists('job_categories');
    }
};
