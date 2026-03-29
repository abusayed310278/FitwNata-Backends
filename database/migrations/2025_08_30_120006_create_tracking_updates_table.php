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
        Schema::create('tracking_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_tracking_id')
      ->constrained('order_tracking')
      ->onDelete('cascade');
            $table->string('status');
            $table->string('location');
            $table->text('description')->nullable();
            $table->timestamp('timestamp');
            $table->timestamps();

            $table->index(['order_tracking_id', 'timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_updates');
    }
};
