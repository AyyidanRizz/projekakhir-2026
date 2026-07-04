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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                        'menunggu_validasi_desain',
                        'desain_divalidasi',
                        'desain_ditolak',
                        'pembayaran_diproses',
                        'pembayaran_divalidasi',
                        'pembayaran_gagal',
                        'dalam_produksi',
                        'dikirim',
                        'selesai',
                        'dibatalkan' // <--- Pastikan ini terdaftar di DB
            ])->change();        
        });
    }
};
