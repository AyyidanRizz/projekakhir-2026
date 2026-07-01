<?php

namespace App\Enums;

enum OrderStatus: string
{
    case MENUNGGU_VALIDASI_DESAIN = 'menunggu_validasi_desain';
    case MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';
    case MENUNGGU_VERIFIKASI_PEMBAYARAN = 'menunggu_verifikasi_pembayaran';
    case SIAP_PRODUKSI = 'siap_produksi';
    case SEDANG_DIPRODUKSI = 'sedang_diproduksi';
    case SELESAI_PRODUKSI = 'selesai_produksi';
    case MENUNGGU_PELUNASAN = 'menunggu_pelunasan';
    case MENUNGGU_VERIFIKASI_PELUNASAN = 'menunggu_verifikasi_pelunasan';
    case SIAP_KIRIM = 'siap_kirim';
    case DIKIRIM = 'dikirim';
    case SELESAI = 'selesai';
    case DITOLAK = 'ditolak';
}