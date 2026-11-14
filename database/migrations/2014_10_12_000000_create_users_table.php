<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->enum('role', ['pembeli', 'penjual'])->default('pembeli');
            $table->string('no_hp', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota', 50)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('foto_profil')->nullable();
            $table->timestamps();
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
