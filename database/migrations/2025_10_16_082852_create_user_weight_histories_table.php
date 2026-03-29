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
        Schema::create('user_weight_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->decimal('old_value', 14, 2)->nullable();
            $table->decimal('new_value', 14, 2);
            $table->foreignId('updated_by')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_weight_histories');
    }
};
