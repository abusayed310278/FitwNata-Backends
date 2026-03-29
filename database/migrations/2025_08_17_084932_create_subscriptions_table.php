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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->string('stripe_id')->unique()->comment('The Subscription ID from Stripe');
            $table->string('stripe_status')->comment('Stripe subscription status: active, canceled, trialing, past_due');
            $table->string('status')->nullable()->comment('Legacy field, kept for compatibility');
            $table->string('stripe_price')->nullable()->comment('Stripe Price ID');
            $table->integer('quantity')->default(1)->comment('Subscription quantity');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable()->comment('When the subscription will actually end');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
