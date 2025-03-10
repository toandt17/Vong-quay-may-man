<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Tạo bảng provinces
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Tạo bảng districts
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('province_id');
            $table->string('name');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
        });

        // Tạo bảng wards
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('district_id');
            $table->string('name');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Xóa bảng wards trước
        Schema::dropIfExists('wards');

        // Xóa bảng districts sau
        Schema::dropIfExists('districts');

        // Xóa bảng provinces cuối cùng
        Schema::dropIfExists('provinces');
    }
};
