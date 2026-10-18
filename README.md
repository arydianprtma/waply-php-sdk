# 🚀 Waply PHP & Laravel SDK - WhatsApp Gateway API Indonesia

[![Latest Stable Version](https://img.shields.io/badge/version-1.0.0-emerald.svg)](https://waply.web.id)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4%20%7C%208.x-blue.svg)](https://www.php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Website](https://img.shields.io/badge/Website-waply.web.id-10b981.svg)](https://waply.web.id)

Official PHP & Laravel client library untuk [Waply WhatsApp Gateway](https://waply.web.id). Kirim pesan teks otomatis, notifikasi OTP, broadcast pesan massal anti-banned, dan integrasi WhatsApp REST API di aplikasi PHP atau Laravel dalam hitungan menit.

---

## 📌 Daftar Isi
- [Fitur Utama](#-fitur-utama)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi di Laravel](#-konfigurasi-di-laravel)
- [Contoh Penggunaan di Laravel](#-contoh-penggunaan-di-laravel)
- [Contoh Penggunaan di PHP Native](#-contoh-penggunaan-di-php-native)
- [Menangani Inbound Webhook](#-menangani-inbound-webhook)
- [Tautan Resmi](#-tautan-resmi)
- [Lisensi](#-lisensi)

---

## 🌟 Fitur Utama

- **Multi-Device Auto-Rotate:** Beban pengiriman otomatis dirotasi merata ke semua nomor WhatsApp yang terhubung.
- **Proteksi Anti-Banned & Spintax:** Dukungan sintaks Spintax `{Halo|Hai|Selamat Pagi}` untuk memastikan hash pesan selalu unik.
- **Pengiriman Lengkap:** Teks, media gambar (JPG/PNG), dokumen (PDF/DOCX), video, audio, dan template shortcode.
- **Broadcast Massal Aman:** Dilengkapi jeda acak (delay), batching otomatis, dan human typing delay.
- **Free Trial 500 Pesan:** Coba langsung tanpa perlu kartu kredit di [waply.web.id](https://waply.web.id).

---

## 💻 Persyaratan Sistem

- PHP >= 7.4, 8.0, 8.1, 8.2, atau 8.3
- Ekstensi PHP `curl` dan `json` aktif
- Akun dan API Key aktif dari [Dashboard Waply](https://waply.web.id/dashboard/api-keys)

---

## 📦 Instalasi

Instal paket menggunakan [Composer](https://getcomposer.org):

```bash
composer require waply/waply-php
```

---

## ⚙️ Konfigurasi di Laravel

### 1. Tambahkan ke file `.env`
Buka file `.env` proyek Laravel Anda, lalu tambahkan API Key Waply:

```env
WAPLY_API_KEY=snd_live_YOUR_API_KEY_HERE
WAPLY_BASE_URL=https://waply.web.id
WAPLY_DEVICE_ID=auto_rotate
```

*(Opsional) Publish file konfigurasi jika ingin kustomisasi:*
```bash
php artisan vendor:publish --tag=waply-config
```

---

## ⚡ Contoh Penggunaan di Laravel

### 1. Kirim Pesan Teks (dengan Spintax)
```php
use Waply\Facades\Waply;

// Kirim notifikasi dengan variasi kata pembuka otomatis
$response = Waply::sendText(
    '081234567890',
    '{Halo|Hai|Selamat Siang} Kak Budi, tagihan Anda sebesar Rp 150.000 telah terbit.'
);

if ($response['success']) {
    echo "Pesan berhasil dikirim dengan ID: " . $response['data']['messageId'];
}
```

### 2. Kirim Notifikasi Template
```php
use Waply\Facades\Waply;

$response = Waply::sendTemplate('081234567890', 'tpl_order_notif', [
    'name'     => 'Budi Santoso',
    'order_id' => 'INV-2026-9812',
    'total'    => 'Rp 350.000',
]);
```

### 3. Kirim File PDF / Invoice
```php
use Waply\Facades\Waply;

$response = Waply::sendMedia(
    '081234567890',
    'Berikut adalah invoice pesanan Anda:',
    'https://example.com/invoices/INV-9812.pdf',
    'document',
    'Invoice-INV-9812.pdf'
);
```

### 4. Kirim Broadcast Massal
```php
use Waply\Facades\Waply;

$recipients = [
    ['phoneNumber' => '6281234567890', 'name' => 'Budi Santoso'],
    ['phoneNumber' => '6289876543210', 'name' => 'Siti Aminah'],
];

$response = Waply::createBroadcast(
    'Promo Spesial Akhir Pekan',
    '{Halo|Hai} {{name}}, dapatkan diskon 30% khusus hari ini!',
    $recipients,
    batchSize: 10,
    batchDelaySec: 45
);
```

---

## 🐘 Contoh Penggunaan di PHP Native

Jika Anda tidak menggunakan Laravel, Anda dapat menggunakan `WaplyClient` secara langsung:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Waply\WaplyClient;

$waply = new WaplyClient('snd_live_YOUR_API_KEY');

// Kirim pesan WhatsApp
try {
    $result = $waply->sendText(
        '081234567890',
        'Halo! Ini pesan tes dari Waply PHP SDK.'
    );
    print_r($result);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

---

## 🔔 Menangani Inbound Webhook

Waply dapat mengirimkan HTTP POST secara real-time ke aplikasi Anda saat ada pesan masuk atau status pengiriman terupdate:

```php
// Contoh controller endpoint webhook di Laravel (routes/api.php)
Route::post('/waply/webhook', function (\Illuminate\Http\Request $request) {
    $event = $request->input('event');
    $data  = $request->input('data');

    if ($event === 'message.received') {
        $sender = $data['from'];
        $text   = $data['text'];
        
        // Logika bot atau simpan pesan masuk ke database
        Log::info("Pesan masuk dari $sender: $text");
    }

    return response()->json(['status' => 'ok']);
});
```

---

## 🔗 Tautan Resmi

* **Website Resmi:** [https://waply.web.id](https://waply.web.id)
* **Dokumentasi REST API Lengkap:** [https://waply.web.id/docs](https://waply.web.id/docs)
* **Daftar Akun Free Trial:** [https://waply.web.id/register](https://waply.web.id/register)
* **Status Layanan Gateway:** [https://waply.web.id/status](https://waply.web.id/status)

---

## 📄 Lisensi

Paket ini dirilis di bawah lisensi terbuka [MIT License](LICENSE). Bebas digunakan untuk keperluan komersial maupun personal.
