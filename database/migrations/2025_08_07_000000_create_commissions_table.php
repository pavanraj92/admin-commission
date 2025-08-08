<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();

            // Type of commission: global, category
            $table->enum('type', ['global', 'category'])->default('global');

            // category id (if applicable)
            $table->unsignedBigInteger('category_id')->nullable();

            // Commission calculation method
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');

            // Commission value (e.g., 10% or ₹500)
            $table->decimal('commission_value', 10, 2);

            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
