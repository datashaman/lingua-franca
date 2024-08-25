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
        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('owner_id')
                ->index()
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('name');
            $table->string('handle')->unique();
            $table->text('instructions');
            $table->text('description')->nullable();
            $table->string('model')->default(config('services.openai.model'));
            $table->string('locale', 10)->default(config('app.locale'));
            $table->json('properties')->nullable();
            $table->string('assistant_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bots');
    }
};
