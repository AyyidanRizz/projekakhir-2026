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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->enum('akad', ['salam', 'istishna'])->default('salam');
            $table->enum('status', [
                'menunggu_validasi_desain',
                'menunggu_pembayaran',
                'menunggu_verifikasi_pembayaran',
                'siap_produksi',
                'sedang_diproduksi',
                'selesai_produksi', 
                'menunggu_pelunasan',
                'menunggu_verifikasi_pelunasan',
                'siap_kirim',
                'dikirim',
                'selesai',
                'ditolak'
            ])->default('menunggu_validasi_desain');
            $table->decimal('total_price', 15, 2);
            $table->decimal('dp_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->text('shipping_address')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('order_date')->useCurrent();
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
