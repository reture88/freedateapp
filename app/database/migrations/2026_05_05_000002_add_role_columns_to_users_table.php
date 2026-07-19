<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('users', static function (Blueprint $table): void {
            $table->string('role', 30)->default('customer')->after('password');
            $table->boolean('is_admin')->default(false)->after('role');
        });

        DB::table('users')
            ->whereNull('role')
            ->orWhere('role', '')
            ->update(['role' => 'customer']);
    }

    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table): void {
            $table->dropColumn(['role', 'is_admin']);
        });
    }
};
