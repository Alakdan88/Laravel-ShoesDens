<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sesuaikan tipe dengan users.id (int signed)
        DB::statement(
            'ALTER TABLE pesanan MODIFY diverifikasi_oleh INT NULL'
        );

        // Buat foreign key setelah tipe data kompatibel
        Schema::table('pesanan', function (Blueprint $table) {
            $table->foreign('diverifikasi_oleh')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Hanya hapus foreign key.
        // Kolom pembayaran dipertahankan untuk menghindari kehilangan data.
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropForeign(['diverifikasi_oleh']);
        });
    }
};
