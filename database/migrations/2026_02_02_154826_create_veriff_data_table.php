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
        Schema::create('veriff_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('session_id')->unique();
            $table->uuid('end_user_id')->nullable();
            $table->string('vendor_data')->nullable();

            $table->string('status')->nullable();
            $table->string('veriff_decision')->nullable();
            $table->string('veriff_reason')->nullable();

            $table->timestamp('veriff_verified_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veriff_data');
    }
};
