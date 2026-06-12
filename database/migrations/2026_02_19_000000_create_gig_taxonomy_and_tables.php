<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gig_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('gig_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gig_category_id')->constrained('gig_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->unique(['gig_category_id', 'slug']);
        });

        Schema::create('gig_service_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gig_subcategory_id')->constrained('gig_subcategories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->unique(['gig_subcategory_id', 'slug']);
        });

        Schema::create('gigs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('gig_category_id')->constrained('gig_categories')->restrictOnDelete();
            $table->foreignId('gig_subcategory_id')->nullable()->constrained('gig_subcategories')->nullOnDelete();
            $table->foreignId('gig_service_type_id')->nullable()->constrained('gig_service_types')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->json('search_tags')->nullable();
            $table->json('requirement_questions')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('gallery')->nullable();
            $table->string('video_path')->nullable();
            $table->json('document_paths')->nullable();
            $table->enum('status', ['active', 'paused', 'deleted'])->default('active');
            $table->timestamps();
            $table->index(['gig_category_id', 'gig_subcategory_id']);
        });

        Schema::create('gig_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gig_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('revisions')->default(0);
            $table->integer('delivery_days');
            $table->timestamps();
            $table->unique(['gig_id', 'type']);
        });

        Schema::create('gig_orders', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 26)->nullable()->unique();
            $table->foreignId('gig_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gig_package_id')->constrained('gig_packages')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'delivered', 'completed', 'revision_requested', 'cancelled'])->default('pending');
            $table->text('clarification_message')->nullable();
            $table->timestamps();
        });

        Schema::create('gig_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gig_id')->constrained('gigs')->cascadeOnDelete();
            $table->string('question');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('gig_requirements');
        Schema::dropIfExists('gig_orders');
        Schema::dropIfExists('gig_packages');
        Schema::dropIfExists('gigs');
        Schema::dropIfExists('gig_service_types');
        Schema::dropIfExists('gig_subcategories');
        Schema::dropIfExists('gig_categories');
    }
};
