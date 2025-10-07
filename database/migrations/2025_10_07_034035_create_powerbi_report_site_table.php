<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('powerbi_report_site', function (Blueprint $table) {
            $table->uuid('report_id');
            $table->uuid('site_id');
            $table->timestamps();

            $table->primary(['report_id','site_id']);

            $table->foreign('report_id')
                ->references('id')->on('powerbi_reports')
                ->cascadeOnDelete();

            $table->foreign('site_id')
                ->references('id')->on('sites')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('powerbi_report_site');
    }
};
