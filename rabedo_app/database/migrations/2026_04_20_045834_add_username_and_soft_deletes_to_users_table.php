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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable();
            $table->softDeletes();
        });

        // Set default username for existing records using a unique string based on email
        $users = \Illuminate\Support\Facades\DB::table('users')->whereNull('username')->get();
        foreach ($users as $key => $user) {
            $baseUsername = \Illuminate\Support\Str::slug(explode('@', $user->email)[0]);
            $username = $baseUsername . '-' . ($user->id ?? $key);
            \Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropSoftDeletes();
        });
    }
};
