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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('upload_document_status')->nullable();
            $table->string('kyc_status')->nullable();
            $table->string('notarization_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('upload_document_status');
            $table->dropColumn('kyc_status');
            $table->dropColumn('notarization_status');
        });
    }
};
