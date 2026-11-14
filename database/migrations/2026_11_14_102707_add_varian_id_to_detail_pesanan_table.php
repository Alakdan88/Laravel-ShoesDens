<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->integer('varian_id')
                ->nullable()
                ->after('produk_id');

            $table->foreign('varian_id')
                ->references('id')
                ->on('produk_varian')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropForeign(['varian_id']);
            $table->dropColumn('varian_id');
        });
    }
};
