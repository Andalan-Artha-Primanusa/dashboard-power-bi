<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('code', 50)->unique();   // ex: ABN, AAP, ABC
            $table->string('name', 150);
            $table->string('description', 500)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
