<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class OllamaService
{
    public function parseCommand(string $text)
    {
        $prompt = "
Kamu adalah AI routing system untuk aplikasi klinik.

Ubah kalimat user menjadi JSON saja.

MENU VALID:
- dashboard-admin
- registrasi
- master-user
- master-anak
- jadwal-terapi
- laporan-pembayaran

OUTPUT HARUS JSON SAJA:

Jika hanya navigasi:
{
  \"menu\": \"master-user\"
}

Jika ada pencarian:
{
  \"action\": \"search\",
  \"target\": \"anak\",
  \"query\": \"Budi\"
}

Kalimat user:
{$text}
        ";

        $response = Http::timeout(60)->post('http://localhost:11434/api/generate', [
            'model' => 'llama3.2',
            'prompt' => $prompt,
            'stream' => false
        ]);

        $raw = $response['response'] ?? '{}';

        preg_match('/\{.*\}/s', $raw, $match);

        return json_decode($match[0] ?? '{}', true);
    }
}