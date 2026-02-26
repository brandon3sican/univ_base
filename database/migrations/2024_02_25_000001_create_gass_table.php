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
        Schema::create('gass', function (Blueprint $table) {
            $table->id();
            
            $table->string('program_project_activity');
            $table->text('output_indicators')->nullable();
            $table->string('office')->nullable();
            
            // Using text from the beginning (as per your second migration intent)
            $table->text('universe')->nullable();
            $table->text('accomplishment')->nullable();
            
            $table->text('remarks')->nullable();

            // Years / targets (you'll likely add these based on the table structure)
            $table->string('target_2024')->nullable();
            $table->string('target_2025')->nullable();
            $table->string('target_2026')->nullable();
            $table->string('target_2027')->nullable();
            $table->string('target_2028')->nullable();

            // Hierarchical / ordering fields
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order_column')->default(0);
            $table->string('record_type')->nullable();

            $table->timestamps();

            // Foreign key for self-referencing hierarchy
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('gass')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gass');
    }
};
