<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppa_id')->nullable()->constrained('ppa')->onDelete('set null');
            $table->foreignId('indicator_id')->nullable()->constrained('indicators')->onDelete('set null');
            $table->json('office_id')->nullable();
            $table->json('universe')->nullable();
            $table->json('accomplishment')->nullable();
            $table->json('targets')->nullable();
            $table->text('remarks')->nullable();
            $table->json('years')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nra');
    }
};
