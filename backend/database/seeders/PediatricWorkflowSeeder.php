<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guarantor;
use App\Models\TherapyPackage;
use App\Models\ProfileAnak;
use App\Models\PaketTerapiPasien;
use App\Models\PatientInsurance;
use App\Models\RegistrasiAnak;
use App\Models\Layanan;
use Carbon\Carbon;

class PediatricWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        // 1. GUARANTORS (MASTER PENJAMIN)
        $guarantors = [
            [
                'kode_penjamin' => 'BPJS-01',
                'nama_penjamin' => 'BPJS Kesehatan',
                'tipe' => 'bpjs',
                'kontak_klaim' => '1500400',
                'email_klaim' => 'klaim@bpjs-kesehatan.go.id',
                'alamat' => 'Jl. Letjen Suprapto Kav. 20 No. 14, Cempaka Putih, Jakarta Pusat',
                'status_aktif' => true,
            ],
            [
                'kode_penjamin' => 'PRU-01',
                'nama_penjamin' => 'Prudential Life Assurance',
                'tipe' => 'asuransi_swasta',
                'kontak_klaim' => '1500085',
                'email_klaim' => 'customer.care@prudential.co.id',
                'alamat' => 'Prudential Tower, Jl. Jend. Sudirman Kav. 79, Jakarta Selatan',
                'status_aktif' => true,
            ],
            [
                'kode_penjamin' => 'MND-01',
                'nama_penjamin' => 'Mandiri Inhealth',
                'tipe' => 'asuransi_swasta',
                'kontak_klaim' => '021-5250900',
                'email_klaim' => 'claim.services@mandiriinhealth.co.id',
                'alamat' => 'Menara Bapindo, Jl. Jend. Sudirman Kav. 54-55, Jakarta',
                'status_aktif' => true,
            ],
            [
                'kode_penjamin' => 'ADM-01',
                'nama_penjamin' => 'AdMedika (Telkom Group)',
                'tipe' => 'asuransi_swasta',
                'kontak_klaim' => '021-29647200',
                'email_klaim' => 'provider.support@admedika.co.id',
                'alamat' => 'Gedung Telkom STO Gambir, Jl. Medan Merdeka Selatan, Jakarta',
                'status_aktif' => true,
            ],
        ];

        foreach ($guarantors as $g) {
            Guarantor::updateOrCreate(['kode_penjamin' => $g['kode_penjamin']], $g);
        }

        // 2. MASTER THERAPY PACKAGES
        $layananWicara = Layanan::where('layanan', 'ilike', '%wicara%')->first();
        $layananOkupasi = Layanan::where('layanan', 'ilike', '%okupasi%')->first();
        $layananSensori = Layanan::where('layanan', 'ilike', '%sensori%')->first() ?? $layananOkupasi;

        $packages = [
            [
                'nama_paket' => 'Paket Terapi Wicara 4 Sesi',
                'layanan_id' => $layananWicara?->id,
                'jumlah_sesi' => 4,
                'harga_paket' => 800000,
                'masa_berlaku_hari' => 90,
                'deskripsi' => 'Paket terapi wicara intensif 4 pertemuan untuk stimulasi artikulasi dan ekspresi.',
                'status_aktif' => true,
            ],
            [
                'nama_paket' => 'Paket Terapi Wicara 8 Sesi',
                'layanan_id' => $layananWicara?->id,
                'jumlah_sesi' => 8,
                'harga_paket' => 1500000,
                'masa_berlaku_hari' => 180,
                'deskripsi' => 'Paket terapi wicara komprehensif 8 pertemuan dengan evaluasi berkala.',
                'status_aktif' => true,
            ],
            [
                'nama_paket' => 'Paket Okupasi Terapi 12 Sesi',
                'layanan_id' => $layananOkupasi?->id,
                'jumlah_sesi' => 12,
                'harga_paket' => 2100000,
                'masa_berlaku_hari' => 180,
                'deskripsi' => 'Paket okupasi terapi motorik halus & kemandirian 12 sesi berkelanjutan.',
                'status_aktif' => true,
            ],
            [
                'nama_paket' => 'Paket Sensori Integrasi 8 Sesi',
                'layanan_id' => $layananSensori?->id,
                'jumlah_sesi' => 8,
                'harga_paket' => 1600000,
                'masa_berlaku_hari' => 180,
                'deskripsi' => 'Paket stimulasi vestibular dan propioseptif pada sensori integrasi anak.',
                'status_aktif' => true,
            ],
        ];

        foreach ($packages as $pkg) {
            TherapyPackage::updateOrCreate(['nama_paket' => $pkg['nama_paket']], $pkg);
        }

        // 3. SAMPLE DATA LINKAGE UNTUK PASIEN ANAK
        $anak1 = ProfileAnak::first();
        $anak2 = ProfileAnak::skip(1)->first();

        $gMandiri = Guarantor::where('kode_penjamin', 'MND-01')->first();
        $gPrudential = Guarantor::where('kode_penjamin', 'PRU-01')->first();

        if ($anak1) {
            // Berikan paket aktif untuk anak 1
            PaketTerapiPasien::updateOrCreate(
                ['nomor_paket' => 'PKT-WICARA-8X-001'],
                [
                    'profile_anak_id' => $anak1->id,
                    'layanan_id' => $layananWicara?->id ?? 1,
                    'nama_paket' => 'Paket Terapi Wicara 8 Sesi',
                    'total_sesi' => 8,
                    'sesi_terpakai' => 2,
                    'sisa_sesi' => 6,
                    'tanggal_mulai' => now()->subWeeks(2)->toDateString(),
                    'tanggal_berakhir' => now()->addMonths(5)->toDateString(),
                    'status' => 'aktif',
                ]
            );
        }

        if ($anak2 && $gMandiri) {
            // Berikan Surat Jaminan (GL) Asuransi aktif untuk anak 2
            PatientInsurance::updateOrCreate(
                ['no_surat_jaminan' => 'GL-MND-2026-00892'],
                [
                    'profile_anak_id' => $anak2->id,
                    'guarantor_id' => $gMandiri->id,
                    'no_polis' => 'POLIS-88291039',
                    'tgl_berlaku_mulai' => now()->subMonth()->toDateString(),
                    'tgl_berlaku_selesai' => now()->addMonths(2)->toDateString(),
                    'plafon_nominal' => 5000000,
                    'kuota_sesi' => 10,
                    'sisa_kuota_sesi' => 8,
                    'status_aktif' => true,
                    'catatan' => 'Plafon terapi tumbuh kembang disetujui 10 sesi evaluasi.',
                ]
            );
        }

        // Update beberapa registrasi eksisting agar memiliki data track yang kaya
        $regList = RegistrasiAnak::all();
        foreach ($regList as $idx => $r) {
            if ($idx % 3 === 0) {
                $r->payment_track = 'umum_package';
                $r->sesi_ke = 3;
                $r->total_sesi = 8;
                $r->status_sesi = 'terjadwal';
                $r->antrian_nomor = 'TW-0' . ($idx + 1);
            } elseif ($idx % 3 === 1) {
                $r->payment_track = 'asuransi';
                $r->guarantor_id = $gMandiri?->id;
                $r->sesi_ke = 2;
                $r->total_sesi = 10;
                $r->status_sesi = 'menunggu';
                $r->status_kedatangan = 'sudah_datang';
                $r->waktu_kedatangan = now();
                $r->antrian_nomor = 'OT-0' . ($idx + 1);
            } else {
                $r->payment_track = 'umum_single';
                $r->sesi_ke = 1;
                $r->total_sesi = 1;
                $r->status_sesi = 'selesai_terapi';
                $r->status_kedatangan = 'sudah_datang';
                $r->status_pelayanan = 'selesai';
                $r->antrian_nomor = 'FT-0' . ($idx + 1);
                $r->mood_anak = 'kooperatif';
                $r->home_program_catatan = 'Latihan meniup lilin dari jarak 30cm dan menirukan vokal A, I, U 10x setiap pagi.';
            }
            $r->save();
        }
    }
}
