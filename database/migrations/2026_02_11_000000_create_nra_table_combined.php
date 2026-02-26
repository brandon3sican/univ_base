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
            $table->string('program_project_activity');
            $table->text('output_indicators')->nullable();
            $table->string('office')->nullable();
            $table->integer('universe')->nullable();
            $table->integer('accomplishment')->nullable();
            $table->integer('order_column')->default(0);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('record_type')->nullable();
            $table->timestamps();

            // Add foreign key constraint
            $table->foreign('parent_id')->references('id')->on('nra')->onDelete('cascade');
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
