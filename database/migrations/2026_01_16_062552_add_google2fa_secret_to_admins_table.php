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
        Schema::table('admins', function (Blueprint $table) {
            $table->text('google2fa_secret')->nullable();
            $table->integer('google2fa_status')->nullable();
            $table->text('two_factor_recovery_token')->nullable();
            $table->string('two_factor_recovery_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('google2fa_secret');
            $table->dropColumn('google2fa_status');
            $table->dropColumn('two_factor_recovery_token');
            $table->dropColumn('two_factor_recovery_expires_at');
        });
    }
};
