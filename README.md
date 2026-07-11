# 👕 Platform Penjualan Sablon & Bordir Berbasis Syariah (Akad Salam & Istishna')

[![Laravel Version](https://img.shields.io/badge/Laravel-v12.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Filament Version](https://img.shields.io/badge/Filament-v3.x-FDAE17?logo=filament&logoColor=black)](https://filamentphp.com)
[![PHP Version](https://img.shields.io/badge/PHP-v8.2+-777BB4?logo=php&logoColor=white)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Supported-2496ED?logo=docker&logoColor=white)](https://www.docker.com)

Proyek ini merupakan **Tugas Akhir Mata Kuliah Pemrograman Web (Semester 4)** di Universitas Esa Unggul. Aplikasi ini adalah platform e-commerce / custom order khusus untuk bisnis penjualan produk **sablon dan bordir** yang dirancang dengan mematuhi prinsip-prinsip syariah Islam, menggunakan implementasi **Akad Salam** dan **Akad Istishna'**.

---

## 📌 Latar Belakang & Konsep Syariah

Dalam perdagangan konvensional, sistem *pre-order* atau pemesanan kustom sering kali menghadapi isu *gharar* (ketidakpastian) karena barang belum ada saat transaksi terjadi. Platform ini hadir untuk memitigasi hal tersebut dengan menerapkan dua akad muamalah yang sah secara syariah:

1. **Akad Salam (Pemesanan Barang Standar/Massal):**
   * Digunakan untuk pemesanan produk sablon/bordir dengan spesifikasi yang sudah distandarisasi oleh sistem (misal: kaos polos dengan desain *ready-stock*).
   * **Ketentuan:** Pembayaran dilakukan penuh di awal (*al-muslam fihi* jelas), dan barang diserahkan kemudian sesuai waktu yang disepakati.

2. **Akad Istishna' (Pemesanan Barang Custom/Pabrikasi):**
   * Digunakan untuk pemesanan produk kustom yang memerlukan proses pembuatan khusus sesuai keinginan konsumen (misal: seragam kemeja bordir komunitas dengan ukuran khusus dan bahan spesifik).
   * **Ketentuan:** Pembayaran dapat dilakukan di awal, dicicil (termin), atau di akhir sesuai kesepakatan tertulis sebelum proses produksi dimulai.

---

## 🚀 Fitur Utama

* **Sistem Transaksi Syariah:** Pemilihan jenis akad (Salam / Istishna') yang disertai lembar kesepakatan digital (*ijab qabul*)[cite: 1].
* **Manajemen Kustomisasi Produk:** Fitur bagi pelanggan untuk mengunggah desain sablon/bordir, memilih jenis bahan, ukuran, dan kuantitas[cite: 1].
* **Dashboard Admin Powerful:** Menggunakan **Filament v3** untuk pengelolaan data produk, pelacakan pesanan, status produksi, hingga validasi pembayaran secara *real-time*[cite: 1].
* **Manajemen Pengguna:** Multi-user role (Admin, Pembuat/Pengrajin, dan Pelanggan)[cite: 1].
* **Pelacakan Status Produksi:** Transparansi pengerjaan pesanan dari tahap antrean, proses sablon/bordir, *quality control*, hingga pengiriman[cite: 1].

---

## 🛠️ Teknologi yang Digunakan

* **Backend Framework:** Laravel 12[cite: 1]
* **Admin Panel:** Filament v3[cite: 1]
* **Bahasa Pemrograman:** PHP 8.2+ & JavaScript (ES6+)[cite: 1]
* **Frontend UI:** HTML5, CSS3, Blade Templating Engine, & Bootstrap[cite: 1]
* **Containerization:** Docker (Optional untuk standarisasi environment development)[cite: 1]
* **Database:** MySQL / PostgreSQL[cite: 1]

---

## 💻 Panduan Instalasi Lokal

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer Anda:

### 1. Klon Repositori
```bash
git clone [https://github.com/AyyidanRizz/projekakhir-2026.git](https://github.com/AyyidanRizz/projekakhir-2026.git)
cd projekakhir-2026