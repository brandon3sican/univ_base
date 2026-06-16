<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ub_offices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('office_types_id')->constrained('ub_office_types')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ub_offices');
    }
};
