<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        $users = DB::table('users')->select('id', 'name')->whereNull('slug')->get();

        foreach ($users as $user) {
            $base = Str::slug((string) $user->name);
            if ($base === '') {
                $base = 'user';
            }

            do {
                $slug = $base . '-' . Str::random(8);
                $exists = DB::table('users')->where('slug', $slug)->exists();
            } while ($exists);

            DB::table('users')->where('id', $user->id)->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
