<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    // Menu valid per role — slug => path
    protected array $menuByRole = [
        'admin' => [
            'dashboard'                 => '/dashboard',
            'registrasi'                => '/registrasi',
            'input-layanan'             => '/input-layanan',
            'list-pendaftaran-layanan'  => '/list-pendaftaran-layanan',
            'list-reschedule'           => '/list-reschedule',
            'pembayaran-tagihan'        => '/pembayaran-tagihan',
            'keluhan-admin'             => '/keluhan-admin',
            'pemanggilan-antrian'       => '/pemanggilan-antrian',
            'master-user'               => '/master-user',
            'master-role'               => '/master-role',
            'master-anak'               => '/master-anak',
            'master-terapis'            => '/master-terapis',
            'master-jadwal-terapis'     => '/master-jadwal-terapis',
            'master-ruangan'            => '/master-ruangan',
            'master-kategori-layanan'   => '/master-kategori-layanan',
            'master-layanan'            => '/master-layanan',
            'master-promosi'           => '/master-promosi',
            'laporan-pembayaran'        => '/laporan-pembayaran',
            'laporan-keluhan'           => '/laporan-keluhan',
            'laporan-histori-promosi'   => '/laporan-histori-promosi',
        ],
        'orang_tua' => [
            'dashboard'                  => '/dashboard-orang-tua',
            'registrasi'                 => '/registrasi',
            'pembayaran-anak'            => '/riwayat-pembayaran-anak',
            'jadwal-terapi'              => '/catatan-aktivitas-anak',
            'monitoring-terapi'          => '/hasil-evaluasi',
            'keluhan'                    => '/keluhan-anak',
        ],
        'terapis' => [
            'dashboard'                  => '/dashboard-terapis',
            'jadwal-terapi'              => '/jadwal-terapi',
            'report-catatan-aktivitas'   => '/report-catatan-aktivitas',
            'evaluasi-terapi'            => '/evaluasi-terapi',
            'riwayat-evaluasi'           => '/riwayat-evaluasi',
        ],
    ];

    protected array $searchableTargets = [
        'admin' => ['anak', 'user', 'terapis', 'pembayaran', 'layanan'],
        'orang_tua' => ['pembayaran', 'jadwal'],
        'terapis' => ['jadwal', 'evaluasi'],
    ];

    public function parseCommand(string $text, string $role): array
    {
        $role = $this->normalizeRole($role);

        if (!isset($this->menuByRole[$role])) {
            return ['menu' => 'unknown', 'error' => 'Role tidak valid'];
        }

        $prompt = $this->buildPrompt($text, $role);

        try {
            $response = Http::timeout(30)->post('http://localhost:11434/api/generate', [
                'model' => 'llama3.2',
                'prompt' => $prompt,
                'stream' => false,
                'format' => 'json',
                'options' => ['temperature' => 0],
            ]);
        } catch (\Throwable $e) {
            Log::error('Ollama request failed: '.$e->getMessage());
            return $this->fallback($text);
        }

        if (!$response->successful()) {
            Log::error('Ollama non-200: '.$response->status());
            return $this->fallback($text);
        }

        $raw = $response['response'] ?? '{}';
        $data = $this->extractJson($raw);

        return $this->validate($data, $role, $text);
    }

    protected function normalizeRole(string $role): string
    {
        $map = [
            'admin' => 'admin',
            '13' => 'admin',
            'terapis' => 'terapis',
            '14' => 'terapis',
            'orang_tua' => 'orang_tua',
            'orangtua' => 'orang_tua',
            '15' => 'orang_tua',
        ];

        return $map[strtolower($role)] ?? 'unknown';
    }

    protected function buildPrompt(string $text, string $role): string
    {
        $menuList = implode("\n", array_map(
            fn ($slug) => "- {$slug}",
            array_keys($this->menuByRole[$role])
        ));

        $targets = implode('|', $this->searchableTargets[$role] ?? []);

        return <<<PROMPT
Kamu adalah AI routing system untuk aplikasi klinik. Tugasmu HANYA mengubah kalimat user menjadi satu objek JSON, tanpa penjelasan, tanpa markdown, tanpa teks tambahan apapun.

User saat ini login sebagai: {$role}

MENU VALID UNTUK ROLE INI (gunakan persis salah satu dari ini):
{$menuList}

ATURAN:
1. Jika user hanya ingin pindah halaman, balas:
{"menu": "<salah satu slug menu di atas>"}

2. Jika user ingin mencari/filter data, balas:
{"action": "search", "target": "<{$targets}>", "query": "<kata kunci>"}

3. Jika maksud user tidak jelas atau tidak ada menu yang cocok, balas:
{"menu": "unknown"}

JANGAN menambahkan teks, alasan, atau format markdown apapun. Hanya satu objek JSON murni.

Kalimat user: "{$text}"
PROMPT;
    }

    protected function extractJson(string $raw): array
    {
        $raw = preg_replace('/```json|```/', '', $raw);

        if (preg_match('/\{.*?\}/s', $raw, $match)) {
            $decoded = json_decode($match[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        Log::warning('Failed to parse AI JSON output', ['raw' => $raw]);
        return [];
    }

    protected function validate(array $data, string $role, string $originalText): array
    {
        $validMenus = $this->menuByRole[$role] ?? [];
        $validTargets = $this->searchableTargets[$role] ?? [];

        if (isset($data['menu'])) {
            if (array_key_exists($data['menu'], $validMenus)) {
                return [
                    'menu' => $data['menu'],
                    'path' => $validMenus[$data['menu']],
                ];
            }
            return ['menu' => 'unknown', 'original' => $originalText];
        }

        if (isset($data['action']) && $data['action'] === 'search') {
            if (in_array($data['target'] ?? null, $validTargets, true) && !empty($data['query'])) {
                return [
                    'action' => 'search',
                    'target' => $data['target'],
                    'query' => $data['query'],
                ];
            }
        }

        return $this->fallback($originalText);
    }

    protected function fallback(string $text): array
    {
        return [
            'menu' => 'unknown',
            'original' => $text,
            'error' => 'AI tidak dapat memproses perintah ini',
        ];
    }
}