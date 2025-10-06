<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->uuid('causer_id')->nullable()->index();   // user pelaku
            $t->string('action');                          // ex: 'user.update_division'
            $t->string('subject_type')->nullable();        // App\Models\User, App\Models\PowerBiReport, etc
            $t->uuid('subject_id')->nullable()->index();   // id objek terkait
            $t->string('ip')->nullable();
            $t->text('user_agent')->nullable();
            $t->json('payload')->nullable();               // detail (sebelum/sesudah)
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
