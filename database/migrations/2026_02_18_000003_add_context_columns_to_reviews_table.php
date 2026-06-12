<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'proposal_id')) {
                $table->unsignedBigInteger('proposal_id')->nullable()->after('job_id');
            }
            if (!Schema::hasColumn('reviews', 'gig_order_id')) {
                $table->unsignedBigInteger('gig_order_id')->nullable()->after('gig_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'proposal_id')) {
                $table->dropColumn('proposal_id');
            }
            if (Schema::hasColumn('reviews', 'gig_order_id')) {
                $table->dropColumn('gig_order_id');
            }
        });
    }
};
