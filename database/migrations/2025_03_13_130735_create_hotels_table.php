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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            // 
            $table->string('name', 255);
            $table->string('address');
            $table->string('city', 100);
            $table->string('country', 100);
            $table->text('description')->nullable();

            $table->string('profile_path', 255)->nullable();
            $table->string('cover_path', 255)->nullable();

            $table->json('coordinate');

            $table->foreignId('owner_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // indexes
            $table->index('name');
            $table->index('city');
            $table->index('country');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
