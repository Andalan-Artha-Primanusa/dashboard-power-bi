<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('divisions', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('name');
            $t->string('code')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });

        // Tambah FK setelah divisions ada
        Schema::table('users', function (Blueprint $t) {
            $t->foreign('division_id')->references('id')->on('divisions')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropForeign(['division_id']);
        });
        Schema::dropIfExists('divisions');
    }
};
