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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('province');
            $table->string('district');
            $table->string('ward');
            $table->string('address');
            $table->boolean('is_farmer')->default(false);
            $table->string('rice_variety')->nullable(); // Giống lúa
            $table->string('rice_stage')->nullable(); // Giai đoạn sinh trưởng
            $table->string('used_products')->nullable(); // Sản phẩm đã sử dụng của Agrijapan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
