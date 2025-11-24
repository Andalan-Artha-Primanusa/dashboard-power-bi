<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {

        // default company buat auto-set session pertama kali
        $table->uuid('default_company_id')->nullable()->after('id');

        $table->index('default_company_id');

        $table->foreign('default_company_id')
            ->references('id')->on('companies')
            ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['default_company_id']);
        $table->dropIndex(['default_company_id']);
        $table->dropColumn(['default_company_id']);
    });
}

};
