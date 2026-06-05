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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total', 10, 2)->nullable();
            $table->string('metode_bayar')->nullable();
            $table->string('status_bayar')->nullable();
            $table->enum('status', ['pesanan_baru',
                        'sedang_diproses',
                        'dikirim',
                        'diterima',
                        'dibatalkan'])->default('pesanan_baru');
            $table->string('mata_uang')->nullable();
            $table->decimal('ongkir', 10, 2)->nullable();
            $table->string('ekspedisi')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
