<?php

namespace App\Models;

use App\Enums\Akad;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'akad',
        'status',
        'total_price',
        'dp_amount',
        'paid_amount',
        'refund_amount',
        'shipping_address',
        'note',
        'order_date',
        'produced_qty_on_cancel', // Tambahkan ini di fillable untuk mencatat history
        'cancellation_note',      // Tambahkan ini di fillable untuk alasan pembatalan
    ];

    protected $casts = [
        'akad' => Akad::class,
        'status' => OrderStatus::class,
        'order_date' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            // Generate order number otomatis jika belum ada
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid()) . '-' . time();
            }
        });
    }

    // ==================== RELASI ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrdersItems::class, 'order_id');
    }

    public function design()
    {
        return $this->hasOne(Designs::class, 'order_id');
    }

    public function payment()
    {
        return $this->hasMany(Payments::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'order_id');
    }

    public function refund()
    {
        return $this->hasMany(Refunds::class, 'order_id');
    }

    public function refunds()
    {
        return $this->hasMany(Refunds::class, 'order_id');
    }

    public function shipping()
    {
        return $this->hasOne(Shippings::class, 'order_id');
    }

    // ==================== LOGIKA STOK & REFUND ====================

    /**
     * Mengembalikan stok semua item dalam order (menyesuaikan akad syariah saat dibatalkan)
     */
    public function restoreStock(int $producedQty = 0): void
    {
        foreach ($this->items as $item) {
            // Sesuai summary data history kamu, pastikan class model targetnya bernama ProductsVariants
            $variant = ProductsVariants::find($item->product_variant_id);
            
            if ($variant) {
                if ($this->akad === Akad::SALAM) {
                    // Akad Salam: Belum diproduksi sama sekali, kembalikan utuh seluruh kuantitas
                    $variant->increment('stock', $item->quantity);
                } elseif ($this->akad === Akad::ISTISHNA) {
                    // Akad Istishna: Yang sudah diproduksi akan tetap dikirim ke pembeli,
                    // jadi yang kembali ke stok gudang hanya sisa produk yang BELUM masuk produksi.
                    $unproducedQty = $item->quantity - $producedQty;
                    if ($unproducedQty > 0) {
                        $variant->increment('stock', $unproducedQty);
                    }
                }
            }
        }
    }

    /**
     * Cek apakah pesanan bisa dibatalkan berdasarkan Enum OrderStatus
     */
    public function canBeCancelled(): bool
    {
        // Pastikan status adalah instance Enum (aman karena sudah di-cast)
        $statusEnum = $this->status;

        if (!$statusEnum instanceof OrderStatus) {
            return false;
        }

        // Ambil string value dari Enum Akad untuk dilempar ke fungsi isCancelable()
        $akadValue = $this->akad instanceof Akad ? $this->akad->value : (string) $this->akad;

        return $statusEnum->isCancelable($akadValue);
    }

    /**
     * Hitung nominal refund berdasarkan aturan akad syariat
     */
    public function calculateRefundAmount(int $producedQty = 0): float
    {
        if (!$this->canBeCancelled()) {
            return 0;
        }

        // 1. HITUNG REFUND AKAD SALAM
        if ($this->akad === Akad::SALAM) {
            // Belum masuk produksi = uang kembali lunas 100%
            return $this->total_price; 
        }

        // 2. HITUNG REFUND AKAD ISTISHNA
        if ($this->akad === Akad::ISTISHNA) {
            
            // Kasus A: Belum masuk tahap produksi sama sekali -> DP 50% kembali utuh
            if ($this->status !== OrderStatus::DALAM_PRODUKSI) {
                return $this->dp_amount; 
            }

            // Kasus B: Sudah dalam tahap produksi
            $totalQty = 0;
            foreach ($this->items as $item) {
                $totalQty += $item->quantity;
            }

            if ($totalQty == 0) {
                return 0;
            }

            // Nilai harga dasar per pcs produk
            $pricePerPcs = $this->total_price / $totalQty;

            // Batas maksimal pengerjaan tahap awal DP (50% dari total kuantitas)
            $halfQty = $totalQty / 2; 

            // Jika realisasi produksi ternyata sudah melewati atau menyentuh batas 50%,
            // maka DP dianggap habis terpakai untuk membiayai produksi berjalan (Refund = 0)
            if ($producedQty >= $halfQty) {
                return 0;
            }

            // Jika pengerjaan produksi di bawah 50%, hitung sisa pcs yang tersisa dari nilai DP
            $unproducedQtyInDp = $halfQty - $producedQty;

            // Refund hanya menghitung nominal dari sisa pcs yang sama sekali belum disentuh produksi
            return $unproducedQtyInDp * $pricePerPcs;
        }

        return 0;
    }
}