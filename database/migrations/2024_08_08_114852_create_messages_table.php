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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_type')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('receiver_type')->nullable();
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->text('content');
            $table->string('locale')->default('en');
            $table->timestamps();

            $table->index(['sender_type', 'sender_id']);
            $table->index(['receiver_type', 'receiver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
