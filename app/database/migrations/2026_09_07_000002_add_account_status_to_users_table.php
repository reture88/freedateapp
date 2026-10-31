<?php

declare(strict_types=1);

use App\User\Enum\AccountStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('users', static function (Blueprint $table): void {
            $table->string('account_status', 32)
                ->default(AccountStatus::ACTIVE->value)
                ->after('is_active')
                ->index();
        });

        DB::table('users')
            ->whereNotNull('deletion_requested_at')
            ->update([
                'account_status' => AccountStatus::DELETION_REQUESTED->value,
                'is_active' => false,
            ]);

        DB::table('users')
            ->whereNull('deletion_requested_at')
            ->where('is_active', false)
            ->update(['account_status' => AccountStatus::DEACTIVATED->value]);

        DB::table('users')
            ->whereNull('deletion_requested_at')
            ->where('is_active', true)
            ->update(['account_status' => AccountStatus::ACTIVE->value]);
    }

    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table): void {
            $table->dropIndex(['account_status']);
            $table->dropColumn('account_status');
        });
    }
};
