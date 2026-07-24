<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); // Menggunakan string agar fleksibel dan tidak bentrok foreign key
            $table->string('restaurant_id');
            $table->string('name');
            $table->text('address')->nullable();
            $table->double('rating')->default(0);
            $table->text('image_url')->nullable();
            $table->boolean('is_favorite')->default(true);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
}