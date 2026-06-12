<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'candidate_payment_method')) {
                $table->string('candidate_payment_method', 30)->nullable()->after('profile_pic');
            }
            if (!Schema::hasColumn('users', 'candidate_payment_details')) {
                $table->text('candidate_payment_details')->nullable()->after('candidate_payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $dropColumns = [];
            foreach (['candidate_payment_method', 'candidate_payment_details'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $dropColumns[] = $column;
                }
            }
            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
