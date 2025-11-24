<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            // kalau belum ada kolomnya
            if (!Schema::hasColumn('divisions', 'company_id')) {
                $table->uuid('company_id')->nullable()->after('id');

                $table->index('company_id');
                $table->foreign('company_id')
                    ->references('id')->on('companies')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            if (Schema::hasColumn('divisions', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropIndex(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
};
