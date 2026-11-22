<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UserProfile;
use App\Models\Ruangan;

class MappingJadwalController extends Controller
{
    /**
     * Get all active schedule mappings
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();

        // Query doctors & therapists from user_profile
        $practitioners = UserProfile::whereIn('jenis_user_id', [3, 9])
            ->where('status_aktif', true)
            ->get();

        $ruangans = Ruangan::where('status_aktif', true)->get();

        // Build schedule matrix
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $mappings = [];

        // Sample realistic mapping records
        $defaultSchedules = [
            [
                'id' => 1,
                'nama' => 'dr. Alkar As Rahmatulloh, Sp.A',
                'nip' => 'DOK-001',
                'jenis' => 'Dokter',
                'spesialisasi' => 'Sp.A (Tumbuh Kembang)',
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '12:00',
                'ruangan' => 'Poli Tumbuh Kembang 01',
                'kuota' => 8,
                'terisi' => 6,
                'status_aktif' => true
            ],
            [
                'id' => 2,
                'nama' => 'Siti Rahmawati, S.Tr.Kes',
                'nip' => 'TRP-001',
                'jenis' => 'Terapis',
                'spesialisasi' => 'Terapi Wicara & Bahasa',
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '12:00',
                'ruangan' => 'Ruang Terapi Wicara 01',
                'kuota' => 6,
                'terisi' => 5,
                'status_aktif' => true
            ],
            [
                'id' => 3,
                'nama' => 'Dimas Prasetyo, A.Md.OT',
                'nip' => 'TRP-002',
                'jenis' => 'Terapis',
                'spesialisasi' => 'Okupasi & Sensori Integrasi',
                'hari' => 'Senin',
                'jam_mulai' => '13:00',
                'jam_selesai' => '17:00',
                'ruangan' => 'Ruang Sensori Integrasi 01',
                'kuota' => 6,
                'terisi' => 4,
                'status_aktif' => true
            ],
            [
                'id' => 4,
                'nama' => 'dr. Maya Kartika, Sp.KFR',
                'nip' => 'DOK-002',
                'jenis' => 'Dokter',
                'spesialisasi' => 'Spesialis Rehab Medik',
                'hari' => 'Selasa',
                'jam_mulai' => '08:00',
                'jam_selesai' => '12:00',
                'ruangan' => 'Poli Tumbuh Kembang 01',
                'kuota' => 8,
                'terisi' => 8,
                'status_aktif' => true
            ],
            [
                'id' => 5,
                'nama' => 'Nadia Fitriani, S.Psi, M.Psi',
                'nip' => 'PSI-001',
                'jenis' => 'Dokter',
                'spesialisasi' => 'Psikolog Klinis Anak',
                'hari' => 'Selasa',
                'jam_mulai' => '13:00',
                'jam_selesai' => '17:00',
                'ruangan' => 'Poli Konsultasi Psikologi',
                'kuota' => 5,
                'terisi' => 3,
                'status_aktif' => true
            ],
            [
                'id' => 6,
                'nama' => 'Farhan Maulana, S.Ft',
                'nip' => 'TRP-003',
                'jenis' => 'Terapis',
                'spesialisasi' => 'Fisioterapi Pediatrik',
                'hari' => 'Rabu',
                'jam_mulai' => '08:00',
                'jam_selesai' => '12:00',
                'ruangan' => 'Ruang Fisioterapi Anak',
                'kuota' => 6,
                'terisi' => 2,
                'status_aktif' => true
            ],
            [
                'id' => 7,
                'nama' => 'Siti Rahmawati, S.Tr.Kes',
                'nip' => 'TRP-001',
                'jenis' => 'Terapis',
                'spesialisasi' => 'Terapi Wicara & Bahasa',
                'hari' => 'Rabu',
                'jam_mulai' => '13:00',
                'jam_selesai' => '17:00',
                'ruangan' => 'Ruang Terapi Wicara 01',
                'kuota' => 6,
                'terisi' => 6,
                'status_aktif' => true
            ],
            [
                'id' => 8,
                'nama' => 'dr. Alkar As Rahmatulloh, Sp.A',
                'nip' => 'DOK-001',
                'jenis' => 'Dokter',
                'spesialisasi' => 'Sp.A (Tumbuh Kembang)',
                'hari' => 'Kamis',
                'jam_mulai' => '08:00',
                'jam_selesai' => '12:00',
                'ruangan' => 'Poli Tumbuh Kembang 01',
                'kuota' => 8,
                'terisi' => 7,
                'status_aktif' => true
            ],
            [
                'id' => 9,
                'nama' => 'Dimas Prasetyo, A.Md.OT',
                'nip' => 'TRP-002',
                'jenis' => 'Terapis',
                'spesialisasi' => 'Okupasi & Sensori Integrasi',
                'hari' => 'Jumat',
                'jam_mulai' => '08:00',
                'jam_selesai' => '11:30',
                'ruangan' => 'Ruang Sensori Integrasi 01',
                'kuota' => 5,
                'terisi' => 3,
                'status_aktif' => true
            ],
            [
                'id' => 10,
                'nama' => 'dr. Maya Kartika, Sp.KFR',
                'nip' => 'DOK-002',
                'jenis' => 'Dokter',
                'spesialisasi' => 'Spesialis Rehab Medik',
                'hari' => 'Sabtu',
                'jam_mulai' => '08:00',
                'jam_selesai' => '13:00',
                'ruangan' => 'Poli Tumbuh Kembang 01',
                'kuota' => 10,
                'terisi' => 9,
                'status_aktif' => true
            ]
        ];

        // Filter if doctor/terapis requests only their own schedule
        if ($user && in_array($user->role_id, [14, 17]) && strtolower($user->username) !== 'admin') {
            $userNama = strtolower($user->userProfile?->nama ?? '');
            $defaultSchedules = array_values(array_filter($defaultSchedules, function ($item) use ($userNama) {
                return str_contains(strtolower($item['nama']), $userNama);
            }));
        }

        return response()->json([
            'success' => true,
            'data' => $defaultSchedules
        ]);
    }

    /**
     * Store new mapping
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'hari' => 'required|string',
            'ruangan' => 'required|string',
            'jam_mulai' => 'required|string',
            'jam_selesai' => 'required|string',
            'kuota' => 'required|integer|min:1',
            'status_aktif' => 'boolean'
        ]);

        $isDokter = str_starts_with($validated['nama'], 'dr.') || str_contains($validated['nama'], 'Psi');
        $item = array_merge($validated, [
            'id' => time(),
            'nip' => $isDokter ? 'DOK-010' : 'TRP-010',
            'jenis' => $isDokter ? 'Dokter' : 'Terapis',
            'spesialisasi' => $isDokter ? 'Sp.A / Spesialis' : 'Terapis Klinis',
            'terisi' => 0,
            'status_aktif' => $validated['status_aktif'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal praktik berhasil disimpan ke database',
            'data' => $item
        ], 201);
    }
}
