<?php

namespace App\Enums;

enum OrderStatus: string
{
    case MENUNGGU_VALIDASI_DESAIN = 'menunggu_validasi_desain';
    case DESAIN_DIVALIDASI = 'desain_divalidasi';
    case DESAIN_DITOLAK = 'desain_ditolak';
    case PEMBAYARAN_DIPROSES = 'pembayaran_diproses';
    case PEMBAYARAN_DIVALIDASI = 'pembayaran_divalidasi';
    case PEMBAYARAN_GAGAL = 'pembayaran_gagal';
    case DALAM_PRODUKSI = 'dalam_produksi';
    case DIKIRIM = 'dikirim';
    case SELESAI = 'selesai';
    case DIBATALKAN = 'dibatalkan';

    /**
     * Mendapatkan label status untuk ditampilkan di UI
     */
    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU_VALIDASI_DESAIN => 'Menunggu Validasi Desain',
            self::DESAIN_DIVALIDASI => 'Desain Divalidasi',
            self::DESAIN_DITOLAK => 'Desain Ditolak',
            self::PEMBAYARAN_DIPROSES => 'Pembayaran Diproses',
            self::PEMBAYARAN_DIVALIDASI => 'Pembayaran Divalidasi',
            self::PEMBAYARAN_GAGAL => 'Pembayaran Gagal',
            self::DALAM_PRODUKSI => 'Dalam Produksi',
            self::DIKIRIM => 'Dikirim',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }

    /**
     * Cek apakah order bisa dibatalkan
     */
    public function isCancelable(): bool
    {
        return in_array($this, [
            self::MENUNGGU_VALIDASI_DESAIN,
            self::DESAIN_DIVALIDASI,
            self::PEMBAYARAN_DIPROSES,
            self::DALAM_PRODUKSI,
        ]);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DIBATALKAN => 'danger', // Warna Merah
            self::MENUNGGU_VALIDASI_DESAIN => 'warning',
            self::PEMBAYARAN_DIPROSES => 'warning', // Warna Kuning/Oranye
            self::DALAM_PRODUKSI => 'info', // Warna Biru
            // default jika status lain belum diberi warna
            default => 'gray', 
        };
    }
}