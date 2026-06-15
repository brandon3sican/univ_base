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
        Schema::create('edit_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('model_type'); // e.g., 'App\Models\Ppa', 'App\Models\Gass', etc.
            $table->unsignedBigInteger('model_id');
            $table->string('action'); // 'created', 'updated', 'deleted'
            $table->json('changes')->nullable(); // Store the old/new values
            $table->string('description')->nullable(); // Human-readable description
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edit_history');
    }
};
