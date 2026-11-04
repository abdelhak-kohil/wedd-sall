<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name_fr', 150);
            $table->string('name_ar', 150);
            $table->string('slug', 100)->unique();
            $table->string('phone', 30);
            $table->string('whatsapp', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('wilaya', 100)->default('Alger');
            $table->string('commune', 120)->default('Alger Centre');
            $table->text('address_line')->nullable();
            $table->string('nif', 30)->nullable();
            $table->string('nis', 30)->nullable();
            $table->string('rc', 30)->nullable();
            $table->string('logo_path', 255)->nullable();
            $table->string('currency', 5)->default('DZD');
            $table->string('timezone', 50)->default('Africa/Algiers');
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
