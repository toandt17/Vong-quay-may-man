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
        Schema::create('prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lucky_wheel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('background_color')->default('#e74c3c'); // Màu nền cho phân đoạn vòng quay
            $table->string('icon')->nullable(); // Biểu tượng cho phân đoạn
            $table->decimal('win_rate', 5, 2); // Tỷ lệ trúng thưởng (%)
            $table->integer('quantity')->default(0); // Số lượng giải thưởng
            $table->integer('remaining')->default(0); // Số lượng còn lại
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prizes');
    }
};
