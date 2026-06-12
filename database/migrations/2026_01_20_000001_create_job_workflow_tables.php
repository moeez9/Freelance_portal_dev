<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('slug', 26)->nullable()->unique();
            $blueprint->foreignId('job_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $blueprint->text('cover_letter');
            $blueprint->decimal('bid_amount', 10, 2);
            $blueprint->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $blueprint->timestamps();
        });

        Schema::table('jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('jobs', 'accepted_proposal_id')) {
                $table->unsignedBigInteger('accepted_proposal_id')->nullable()->after('status');
            }
        });

        Schema::create('work_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 26)->nullable()->unique();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->string('file_path')->nullable();
            $table->enum('status', ['submitted', 'approved', 'revision_requested'])->default('submitted');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Payer
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'released'])->default('pending');
            $table->string('type'); // job or gig
            $table->unsignedBigInteger('reference_id'); // gig_order_id or job_id
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('job_id')->nullable();
            $table->unsignedBigInteger('gig_id')->nullable();
            $table->integer('rating');
            $table->text('comment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('work_submissions');
        Schema::dropIfExists('proposals');
    }
};
