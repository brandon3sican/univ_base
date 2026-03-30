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
        Schema::create('sto_accomplishments', function (Blueprint $table) {
            $table->id();
            $table->json('office_ids')->nullable();
            $table->json('values')->nullable();
            $table->json('remarks')->nullable();
            $table->json('years')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sto_accomplishments');
    }
};
