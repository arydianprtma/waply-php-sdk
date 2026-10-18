<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Waply API Key
    |--------------------------------------------------------------------------
    |
    | Dapatkan API Key melalui dashboard Waply di menu Dashboard > API Keys.
    | https://waply.web.id/dashboard/api-keys
    |
    */
    'api_key' => env('WAPLY_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Waply Base URL
    |--------------------------------------------------------------------------
    |
    | URL server gateway Waply. Gunakan https://waply.web.id untuk live production.
    |
    */
    'base_url' => env('WAPLY_BASE_URL', 'https://waply.web.id'),

    /*
    |--------------------------------------------------------------------------
    | Default Device ID
    |--------------------------------------------------------------------------
    |
    | Gunakan 'auto_rotate' untuk membagi beban pengiriman secara otomatis
    | ke seluruh device nomor WhatsApp yang sedang aktif dan online.
    |
    */
    'device_id' => env('WAPLY_DEVICE_ID', 'auto_rotate'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Batas waktu HTTP timeout dalam satuan detik (default: 30 detik).
    |
    */
    'timeout' => env('WAPLY_TIMEOUT', 30),
];
