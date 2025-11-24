<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            if (!Schema::hasColumn('sites', 'company_id')) {
                $table->uuid('company_id')->nullable()->after('id');
                $table->index('company_id');

                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->cascadeOnDelete();
            }
        });

        // ===== Backfill data lama =====
        // Ambil 1 company pertama sebagai default untuk sites lama
        $defaultCompanyId = DB::table('companies')
            ->orderBy('created_at')
            ->value('id');

        if ($defaultCompanyId) {
            DB::table('sites')
                ->whereNull('company_id')
                ->update(['company_id' => $defaultCompanyId]);
        }

        // ===== Coba set NOT NULL (butuh doctrine/dbal) =====
        // Kalau ga ada dbal, ini akan gagal tapi kita swallow biar migrate tetap jalan.
        try {
            Schema::table('sites', function (Blueprint $table) {
                $table->uuid('company_id')->nullable(false)->change();
            });
        } catch (\Throwable $e) {
            // skip aja, enforced di validation/controller
        }

        // OPTIONAL (rekomendasi): unique code per company
        // Kalau sebelumnya code unique global, ini bikin lebih fleksibel.
        // Comment kalau kamu mau tetap unique global.
        try {
            Schema::table('sites', function (Blueprint $table) {
                // drop unique lama kalau ada
                // nama index unique beda2 tiap DB, jadi guard try/catch
                $table->dropUnique('sites_code_unique');
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('sites', function (Blueprint $table) {
                $table->unique(['company_id', 'code'], 'sites_company_code_unique');
            });
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        // rollback unique composite kalau ada
        try {
            Schema::table('sites', function (Blueprint $table) {
                $table->dropUnique('sites_company_code_unique');
            });
        } catch (\Throwable $e) {}

        Schema::table('sites', function (Blueprint $table) {
            if (Schema::hasColumn('sites', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropIndex(['company_id']);
                $table->dropColumn('company_id');
            }
        });

        // OPTIONAL: balikin unique global code kalau mau
        // try {
        //     Schema::table('sites', function (Blueprint $table) {
        //         $table->unique('code');
        //     });
        // } catch (\Throwable $e) {}
    }
};
