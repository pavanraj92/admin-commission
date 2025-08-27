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
            $table->foreignId('commission_id')->nullable()->constrained('commissions')->after('id')->onDelete('cascade');
            $table->decimal('commission_value', 10, 2)->nullable()->after('commission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key first if applied
            // $table->dropForeign(['commission_id']);
            $table->dropColumn(['commission_id', 'commission_value']);
        });
    }
};
