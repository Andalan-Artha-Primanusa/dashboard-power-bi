<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Buat tabel divisions
        Schema::create('divisions', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('name')->unique();
            $t->string('code', 20)->unique();
            $t->text('description')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->softDeletes();
        });

        // Tambahkan foreign key ke users.division_id
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users','division_id')) {
                $t->uuid('division_id')->nullable()->after('id');
            }
            $t->foreign('division_id')
                ->references('id')->on('divisions')
                ->nullOnDelete(); // kalau division dihapus -> set null
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropForeign(['division_id']);
        });

        Schema::dropIfExists('divisions');
    }
};
