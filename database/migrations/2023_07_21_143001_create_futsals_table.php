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
        Schema::create('futsals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id');
            $table->string('name');
            $table->text('description');
            $table->string('facilities')->nullable();
            $table->text('cancellation');
            $table->string('whatsapp');
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('avatar')->nullable();
            $table->text('address');
            $table->boolean('isActive')->default(false);
            $table->double('rating')->nullable();
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('futsals');
    }
};
