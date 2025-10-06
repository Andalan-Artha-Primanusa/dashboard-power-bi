<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('code', 20)->unique();   // e.g. DBK, POS, SBS, HO
            $t->string('name');                 // e.g. DBK Kalteng
            $t->string('region')->nullable();   // province / island / area
            $t->string('address')->nullable();
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();
            $t->boolean('is_active')->default(true);

            // konfigurasi per-site (threshold, branding, param komoditas, dll)
            $t->json('config')->nullable();

            $t->uuid('created_by')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
