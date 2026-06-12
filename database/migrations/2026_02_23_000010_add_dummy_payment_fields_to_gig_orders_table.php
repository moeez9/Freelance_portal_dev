<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gig_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('gig_orders', 'payment_method')) {
                $table->string('payment_method', 30)->nullable()->after('clarification_message');
            }
            if (!Schema::hasColumn('gig_orders', 'payer_name')) {
                $table->string('payer_name')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('gig_orders', 'payer_contact')) {
                $table->string('payer_contact', 50)->nullable()->after('payer_name');
            }
            if (!Schema::hasColumn('gig_orders', 'transaction_reference')) {
                $table->string('transaction_reference', 100)->nullable()->after('payer_contact');
            }
            if (!Schema::hasColumn('gig_orders', 'payment_verified_at')) {
                $table->timestamp('payment_verified_at')->nullable()->after('transaction_reference');
            }
            if (!Schema::hasColumn('gig_orders', 'payment_verified_by')) {
                $table->foreignId('payment_verified_by')->nullable()->after('payment_verified_at')
                    ->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('gig_orders', function (Blueprint $table) {
            if (Schema::hasColumn('gig_orders', 'payment_verified_by')) {
                $table->dropConstrainedForeignId('payment_verified_by');
            }
            $dropColumns = [];
            foreach (['payment_method', 'payer_name', 'payer_contact', 'transaction_reference', 'payment_verified_at'] as $column) {
                if (Schema::hasColumn('gig_orders', $column)) {
                    $dropColumns[] = $column;
                }
            }
            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
