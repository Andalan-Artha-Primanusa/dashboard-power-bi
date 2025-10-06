<?php // database/migrations/xxxx_xx_xx_xxxxxx_alter_users_to_uuid_and_division.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // ubah users.id jadi uuid
        Schema::table('users', function (Blueprint $t) {
            $t->dropPrimary();
            $t->uuid('id')->change();
            $t->primary('id');
            // kolom division_id (nullable)
            if (!Schema::hasColumn('users','division_id')) {
                $t->uuid('division_id')->nullable()->after('id');
            }
        });

        // sessions.user_id (nullable uuid)
        Schema::table('sessions', function (Blueprint $t) {
            if (Schema::hasColumn('sessions','user_id')) {
                $t->uuid('user_id')->nullable()->change();
            }
        });
    }
    public function down(): void {}
};
