<?php

namespace Waply;

use Exception;

/**
 * Official Waply WhatsApp Gateway PHP Client
 *
 * @see https://waply.web.id
 * @see https://waply.web.id/docs
 */
class WaplyClient
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $defaultDeviceId;
    protected int $timeout;

    /**
     * Inisialisasi Waply Client
     *
     * @param string $apiKey API Key yang didapatkan dari dashboard Waply (https://waply.web.id/dashboard/api-keys)
     * @param string $baseUrl Base URL gateway (default: https://waply.web.id)
     * @param string $defaultDeviceId Default device ID atau 'auto_rotate'
     * @param int $timeout Request timeout dalam detik (default: 30)
     */
    public function __construct(
        string $apiKey,
        string $baseUrl = 'https://waply.web.id',
        string $defaultDeviceId = 'auto_rotate',
        int $timeout = 30
    ) {
        $this->apiKey = trim($apiKey);
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->defaultDeviceId = $defaultDeviceId;
        $this->timeout = $timeout;
    }

    /**
     * Kirim pesan teks biasa (Mendukung Spintax {Halo|Hai})
     *
     * @param string $to Nomor tujuan format internasional tanpa tanda + (contoh: 628123456789)
     * @param string $message Isi pesan teks (bisa format Spintax)
     * @param string|null $deviceId ID device pengirim atau 'auto_rotate'
     * @return array Response JSON dari server
     * @throws Exception
     */
    public function sendText(string $to, string $message, ?string $deviceId = null): array
    {
        $payload = [
            'to' => $this->normalizePhoneNumber($to),
            'message' => $message,
            'deviceId' => $deviceId ?? $this->defaultDeviceId,
        ];

        return $this->request('POST', '/api/v1/messages/send', $payload);
    }

    /**
     * Kirim pesan menggunakan template tersimpan & variabel dinamis
     *
     * @param string $to Nomor tujuan (contoh: 628123456789)
     * @param string $template Shortcode template yang sudah didaftarkan
     * @param array $variables Key-value variabel template, misal: ['name' => 'Budi', 'order_id' => 'INV-001']
     * @param string|null $deviceId ID device pengirim atau 'auto_rotate'
     * @return array
     * @throws Exception
     */
    public function sendTemplate(string $to, string $template, array $variables = [], ?string $deviceId = null): array
    {
        $payload = [
            'to' => $this->normalizePhoneNumber($to),
            'template' => $template,
            'variables' => $variables,
            'deviceId' => $deviceId ?? $this->defaultDeviceId,
        ];

        return $this->request('POST', '/api/v1/messages/send', $payload);
    }

    /**
     * Kirim pesan media (Gambar, Dokumen PDF, Video, atau Audio)
     *
     * @param string $to Nomor tujuan
     * @param string $message Caption atau pesan pengiring
     * @param string $mediaUrl URL publik file media (harus HTTPS atau HTTP publik)
     * @param string $mediaType 'image', 'document', 'video', atau 'audio'
     * @param string $fileName Nama file lampiran saat diunduh penerima (misal: invoice.pdf)
     * @param string|null $deviceId
     * @return array
     * @throws Exception
     */
    public function sendMedia(
        string $to,
        string $message,
        string $mediaUrl,
        string $mediaType = 'image',
        string $fileName = '',
        ?string $deviceId = null
    ): array {
        $payload = [
            'to' => $this->normalizePhoneNumber($to),
            'message' => $message,
            'mediaUrl' => $mediaUrl,
            'mediaType' => $mediaType,
            'fileName' => $fileName ?: basename($mediaUrl),
            'deviceId' => $deviceId ?? $this->defaultDeviceId,
        ];

        return $this->request('POST', '/api/v1/messages/send', $payload);
    }

    /**
     * Buat kampanye broadcast pesan massal
     *
     * @param string $title Nama/judul kampanye broadcast
     * @param string $template Isi pesan template
     * @param array $recipients Array daftar penerima [['phoneNumber' => '628...', 'name' => '...'], ...]
     * @param int $batchSize Jumlah pesan per batch (default: 10)
     * @param int $batchDelaySec Delay antar batch dalam detik (default: 60)
     * @return array
     * @throws Exception
     */
    public function createBroadcast(
        string $title,
        string $template,
        array $recipients,
        int $batchSize = 10,
        int $batchDelaySec = 60
    ): array {
        $payload = [
            'name' => $title,
            'messageTemplate' => $template,
            'batchSize' => $batchSize,
            'batchDelaySec' => $batchDelaySec,
            'recipients' => $recipients,
        ];

        return $this->request('POST', '/api/broadcast', $payload);
    }

    /**
     * Ambil status seluruh sesi device WhatsApp yang terhubung
     *
     * @return array
     * @throws Exception
     */
    public function getSessions(): array
    {
        return $this->request('GET', '/api/gateway/sessions');
    }

    /**
     * Standarisasi format nomor telepon Indonesia (misal 0812... menjadi 62812...)
     *
     * @param string $phone
     * @return string
     */
    protected function normalizePhoneNumber(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($clean, '08')) {
            return '62' . substr($clean, 1);
        }

        if (str_starts_with($clean, '8')) {
            return '62' . $clean;
        }

        return $clean;
    }

    /**
     * Eksekusi HTTP Request via cURL
     *
     * @param string $method GET|POST|PUT|DELETE
     * @param string $endpoint Path API
     * @param array|null $data Payload data
     * @return array
     * @throws Exception
     */
    protected function request(string $method, string $endpoint, ?array $data = null): array
    {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init();

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: Waply-PHP-SDK/1.0',
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif (strtoupper($method) !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("Waply cURL Error: " . $error);
        }

        $result = json_decode($response, true);
        if ($httpCode >= 400) {
            $msg = $result['message'] ?? $result['error'] ?? "HTTP Error $httpCode";
            throw new Exception("Waply API Error ($httpCode): " . $msg);
        }

        return $result ?? [];
    }
}
