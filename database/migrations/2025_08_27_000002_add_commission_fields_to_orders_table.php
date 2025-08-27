<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $industry = null;
        if (session()->has('industry')) {
            $industry = Session::get('industry');
        } else {
            $industry = DB::table('settings')->where('slug', 'industry')->value('config_value') ?? 'ecommerce';
        }
        if ($industry === 'ecommerce') {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('commission_id')->nullable()->constrained('commissions')->after('id');
                $table->enum('commission_type', ['percentage', 'fixed'])->nullable()->default('percentage')->after('commission_id');
                $table->decimal('commission_value', 10, 2)->nullable()->after('commission_id');
            });
        }elseif($industry === 'education'){
            Schema::table('course_purchases', function (Blueprint $table) {
                $table->foreignId('commission_id')->nullable()->constrained('commissions')->after('id');
                $table->enum('commission_type', ['percentage', 'fixed'])->nullable()->default('percentage')->after('commission_id');
                $table->decimal('commission_value', 10, 2)->nullable()->after('commission_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $industry = null;
        if (session()->has('industry')) {
            $industry = Session::get('industry');
        } else {
            $industry = DB::table('settings')->where('slug', 'industry')->value('config_value') ?? 'ecommerce';
        }

        if ($industry === 'ecommerce') {
            Schema::table('orders', function (Blueprint $table) {
                // Drop foreign key first if applied
                // $table->dropForeign(['commission_id']);
                $table->dropColumn(['commission_id', 'commission_type', 'commission_value']);
            });
        }elseif($industry === 'education'){
            Schema::table('course_purchases', function (Blueprint $table) {
                // Drop foreign key first if applied
                // $table->dropForeign(['commission_id']);
                $table->dropColumn(['commission_id', 'commission_type', 'commission_value']);
            });
        }
    }
};
