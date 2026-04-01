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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                ->constrained() // This will reference the 'id' column on the 'users' table
                ->onDelete('cascade'); // If a user is deleted, their orders will be deleted too

            $table->decimal('total_price', 10, 2);
            $table->string('status')->default('pending'); // e.g., pending, completed, cancelled
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
