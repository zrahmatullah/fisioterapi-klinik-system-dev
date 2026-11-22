<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SmokeDummyDataSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        $todayStr = $now->toDateString();

        // ================= 1. RUANGAN TAMBAHAN =================
        $ruangans = [
            ['id' => 1, 'ruangan' => 'Ruang Sensori Integrasi Utama', 'status_aktif' => true],
            ['id' => 2, 'ruangan' => 'Ruang Terapi Wicara A', 'status_aktif' => true],
            ['id' => 3, 'ruangan' => 'Ruang Terapi Wicara B', 'status_aktif' => true],
            ['id' => 4, 'ruangan' => 'Ruang Terapi Okupasi & Motorik', 'status_aktif' => true],
            ['id' => 5, 'ruangan' => 'Ruang Fisioterapi Pediatrik', 'status_aktif' => true],
            ['id' => 6, 'ruangan' => 'Ruang Asesmen & Observasi Anak', 'status_aktif' => true],
        ];

        foreach ($ruangans as $r) {
            DB::table('ruangan')->updateOrInsert(
                ['id' => $r['id']],
                ['ruangan' => $r['ruangan'], 'status_aktif' => $r['status_aktif'], 'updated_at' => $now]
            );
        }

        // ================= 2. UPDATE PROFILE ANAK LENGKAP =================
        $anakSamples = [
            [
                'id' => 6,
                'nama_anak' => 'Azhar Al-Farizi',
                'no_rm' => 'RM-202512-00006',
                'sekolah' => 'TK Pelita Harapan Bangsa',
                'catatan_alergi' => 'Alergi susu sapi (laktosa) & telur',
                'diagnosis_awal' => 'F80.1 Gangguan Berbahasa Ekspresif (Speech Delay)',
                'rujukan_dari' => 'Dr. Budi Santoso, Sp.A',
            ],
            [
                'id' => 7,
                'nama_anak' => 'Rahma Danisha',
                'no_rm' => 'RM-202512-00007',
                'sekolah' => 'PAUD Melati Indah',
                'catatan_alergi' => 'Tidak ada alergi obat/makanan',
                'diagnosis_awal' => 'F88 Gangguan Perkembangan Sensori (Sensory Processing Disorder)',
                'rujukan_dari' => 'Klinik Tumbuh Kembang Sejahtera',
            ],
            [
                'id' => 8,
                'nama_anak' => 'Hilmi Ramadhan',
                'no_rm' => 'RM-202512-00008',
                'sekolah' => 'SD Inklusi Permata Hati Kelas 1',
                'catatan_alergi' => 'Alergi debu & dingin (asma ringan)',
                'diagnosis_awal' => 'F90.0 Gangguan Aktivitas & Perhatian (ADHD)',
                'rujukan_dari' => 'Psikolog Anak Dra. Endang, M.Psi',
            ],
            [
                'id' => 9,
                'nama_anak' => 'Kenzo Alvaro',
                'no_rm' => 'RM-202512-00009',
                'sekolah' => 'Preschool Little Star',
                'catatan_alergi' => 'Alergi seafood',
                'diagnosis_awal' => 'F84.0 Autisme Masa Kanak-Kanak (Mild ASD)',
                'rujukan_dari' => 'RSIA Bunda Jakarta',
            ],
            [
                'id' => 10,
                'nama_anak' => 'Alesha Putri',
                'no_rm' => 'RM-202512-00010',
                'sekolah' => 'Belum Sekolah',
                'catatan_alergi' => 'Tidak ada riwayat alergi',
                'diagnosis_awal' => 'F82 Gangguan Motorik Kasar & Keseimbangan',
                'rujukan_dari' => 'Dokter Umum Puskesmas',
            ],
        ];

        foreach ($anakSamples as $a) {
            DB::table('profile_anak')->where('id', $a['id'])->update([
                'nama_anak' => $a['nama_anak'],
                'no_rm' => $a['no_rm'],
                'sekolah' => $a['sekolah'],
                'catatan_alergi' => $a['catatan_alergi'],
                'diagnosis_awal' => $a['diagnosis_awal'],
                'rujukan_dari' => $a['rujukan_dari'],
                'updated_at' => $now,
            ]);
        }

        // ================= 3. WALI & KONTAK DARURAT =================
        $walis = [
            ['profile_anak_id' => 6, 'nama_wali' => 'Bambang Sudarmono', 'hubungan' => 'Ayah', 'no_hp' => '081234567890', 'pekerjaan' => 'Karyawan Swasta', 'is_kontak_darurat' => true, 'is_wali_utama' => true],
            ['profile_anak_id' => 6, 'nama_wali' => 'Siti Nurhaliza', 'hubungan' => 'Ibu', 'no_hp' => '081298765432', 'pekerjaan' => 'Ibu Rumah Tangga', 'is_kontak_darurat' => true, 'is_wali_utama' => false],
            ['profile_anak_id' => 7, 'nama_wali' => 'Hendra Kurniawan', 'hubungan' => 'Ayah', 'no_hp' => '081345678901', 'pekerjaan' => 'Wiraswasta', 'is_kontak_darurat' => true, 'is_wali_utama' => true],
            ['profile_anak_id' => 8, 'nama_wali' => 'Farida Utami', 'hubungan' => 'Ibu', 'no_hp' => '081567890123', 'pekerjaan' => 'PNS Guru', 'is_kontak_darurat' => true, 'is_wali_utama' => true],
            ['profile_anak_id' => 9, 'nama_wali' => 'dr. Anton Wijaya', 'hubungan' => 'Ayah', 'no_hp' => '081678901234', 'pekerjaan' => 'Dokter Gigi', 'is_kontak_darurat' => true, 'is_wali_utama' => true],
            ['profile_anak_id' => 10, 'nama_wali' => 'Ratna Dewi', 'hubungan' => 'Ibu', 'no_hp' => '081789012345', 'pekerjaan' => 'Arsitek', 'is_kontak_darurat' => true, 'is_wali_utama' => true],
        ];

        foreach ($walis as $w) {
            DB::table('pasien_wali')->updateOrInsert(
                ['profile_anak_id' => $w['profile_anak_id'], 'nama_wali' => $w['nama_wali']],
                array_merge($w, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // ================= 4. INFORMED CONSENT MEDIS =================
        $consents = [
            [
                'profile_anak_id' => 6,
                'judul_persetujuan' => 'Persetujuan Umum & Tindakan Terapi Wicara',
                'tipe_persetujuan' => 'tindakan_terapi',
                'pernyataan' => 'Saya selaku orang tua menyatakan setuju anak saya mengikuti program terapi wicara dan mengizinkan perekaman observasi untuk evaluasi klinis.',
                'nama_penandatangan' => 'Bambang Sudarmono',
                'hubungan_wali' => 'Ayah Kandung',
                'tanda_tangan_base64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
                'ip_address' => '127.0.0.1',
                'signed_at' => $now->subDays(10),
            ],
            [
                'profile_anak_id' => 7,
                'judul_persetujuan' => 'Persetujuan Tindakan Terapi Sensori Integrasi & Pengolahan Data PDP',
                'tipe_persetujuan' => 'penggunaan_data_pdp',
                'pernyataan' => 'Setuju dengan pemrosesan data rekam medis elektronik sesuai UU PDP No. 27 Tahun 2022.',
                'nama_penandatangan' => 'Hendra Kurniawan',
                'hubungan_wali' => 'Ayah Kandung',
                'tanda_tangan_base64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
                'ip_address' => '127.0.0.1',
                'signed_at' => $now->subDays(5),
            ]
        ];

        foreach ($consents as $c) {
            DB::table('informed_consents')->updateOrInsert(
                ['profile_anak_id' => $c['profile_anak_id'], 'judul_persetujuan' => $c['judul_persetujuan']],
                array_merge($c, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // ================= 5. REGISTRASI KUNJUNGAN HARI INI & MINGGU INI =================
        $registrasis = [
            [
                'id' => 101,
                'profile_anak_id' => 6,
                'id_pasien' => 'PA-6-014',
                'no_regis' => 'REG-' . date('Ymd') . '-0001',
                'no_antrian' => 'A-001',
                'tgl_regis' => $todayStr,
                'terapis_id' => 14,
                'ruangan_id' => 2,
                'status_kedatangan' => 'sudah_datang',
                'status_pelayanan' => 'sedang_dilayani',
                'waktu_kedatangan' => $now->copy()->subMinutes(30),
                'nama_ayah' => 'Bambang Sudarmono',
                'nama_ibu' => 'Siti Nurhaliza',
                'notelp' => '081234567890',
                'keluhan_saat_ini' => 'Ananda belum bisa menyusun 2 kata, sering frustasi saat keinginannya tidak dipahami.',
                'kemampuan_saat_ini' => 'Mampu menirukan bunyi vokal a-i-u-e-o, kontak mata 3-5 detik.',
            ],
            [
                'id' => 102,
                'profile_anak_id' => 7,
                'id_pasien' => 'PA-7-002',
                'no_regis' => 'REG-' . date('Ymd') . '-0002',
                'no_antrian' => 'A-002',
                'tgl_regis' => $todayStr,
                'terapis_id' => 18,
                'ruangan_id' => 1,
                'status_kedatangan' => 'sudah_datang',
                'status_pelayanan' => 'belum_dilayani',
                'waktu_kedatangan' => $now->copy()->subMinutes(10),
                'nama_ayah' => 'Hendra Kurniawan',
                'nama_ibu' => 'Mega Puspita',
                'notelp' => '081345678901',
                'keluhan_saat_ini' => 'Takut pada suara blender dan tekstur lengket, sering jinjit saat berjalan.',
                'kemampuan_saat_ini' => 'Bisa berjalan sendiri, mampu memegang sendok.',
            ],
            [
                'id' => 103,
                'profile_anak_id' => 8,
                'id_pasien' => 'PA-8-002',
                'no_regis' => 'REG-' . date('Ymd') . '-0003',
                'no_antrian' => 'A-003',
                'tgl_regis' => $todayStr,
                'terapis_id' => 23,
                'ruangan_id' => 4,
                'status_kedatangan' => 'belum_datang',
                'status_pelayanan' => 'belum_dilayani',
                'waktu_kedatangan' => null,
                'nama_ayah' => 'Rahmat Hidayat',
                'nama_ibu' => 'Farida Utami',
                'notelp' => '081567890123',
                'keluhan_saat_ini' => 'Sangat hiperaktif di kelas, sulit duduk tenang lebih dari 5 menit.',
                'kemampuan_saat_ini' => 'Komunikasi lancar, daya konsentrasi pendek.',
            ],
            [
                'id' => 104,
                'profile_anak_id' => 9,
                'id_pasien' => 'PA-9-003',
                'no_regis' => 'REG-' . date('Ymd') . '-0004',
                'no_antrian' => 'A-004',
                'tgl_regis' => $todayStr,
                'terapis_id' => 14,
                'ruangan_id' => 2,
                'status_kedatangan' => 'selesai_dilayani',
                'status_pelayanan' => 'selesai_dilayani',
                'waktu_kedatangan' => $now->copy()->subHours(2),
                'nama_ayah' => 'dr. Anton Wijaya',
                'nama_ibu' => 'Dr. Maya Kartika',
                'notelp' => '081678901234',
                'keluhan_saat_ini' => 'Sering flapping tangan saat gembira, minim kontak mata.',
                'kemampuan_saat_ini' => 'Mengenal huruf dan angka visual.',
            ],
        ];

        foreach ($registrasis as $reg) {
            DB::table('registrasi_anak')->updateOrInsert(
                ['id' => $reg['id']],
                array_merge($reg, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // ================= 6. PELAYANAN TERAPI & RESCHEDULE REQUEST =================
        $pelayanans = [
            [
                'id' => 201,
                'registrasi_anak_id' => 101,
                'layanan_id' => 3, // Terapi 1
                'terapis_id' => 14,
                'qty' => 1,
                'harga' => 150000,
                'tanggal_penjadwalan' => $todayStr,
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '11:00:00',
                'status' => 'sedang_berlangsung',
                'tanggal_reschedule_request' => null,
            ],
            [
                'id' => 202,
                'registrasi_anak_id' => 102,
                'layanan_id' => 4, // Terapi Paket 1
                'terapis_id' => 18,
                'qty' => 1,
                'harga' => 150000,
                'tanggal_penjadwalan' => $todayStr,
                'jam_mulai' => '11:00:00',
                'jam_selesai' => '12:00:00',
                'status' => 'belum_mulai',
                'tanggal_reschedule_request' => null,
            ],
            [
                'id' => 203,
                'registrasi_anak_id' => 103,
                'layanan_id' => 3,
                'terapis_id' => 23,
                'qty' => 1,
                'harga' => 150000,
                'tanggal_penjadwalan' => $todayStr,
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '14:00:00',
                'status' => 'pending_reschedule',
                'tanggal_reschedule_request' => $now->copy()->addDays(2)->toDateString(),
            ],
            [
                'id' => 204,
                'registrasi_anak_id' => 104,
                'layanan_id' => 4,
                'terapis_id' => 14,
                'qty' => 1,
                'harga' => 350000,
                'tanggal_penjadwalan' => $todayStr,
                'jam_mulai' => '08:30:00',
                'jam_selesai' => '09:30:00',
                'status' => 'selesai',
                'tanggal_reschedule_request' => null,
            ],
        ];

        foreach ($pelayanans as $pel) {
            DB::table('pelayanan_terapi_anak')->updateOrInsert(
                ['id' => $pel['id']],
                array_merge($pel, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // ================= 7. EMR RECORDS & SOAP KHUSUS TERAPI ANAK =================
        $emrRecords = [
            [
                'id' => 1,
                'no_emr' => 'EMR-202611-0001',
                'profile_anak_id' => 6,
                'registrasi_anak_id' => 101,
                'terapis_id' => 14,
                'tanggal_pemeriksaan' => $todayStr,
                'jenis_layanan' => 'Terapi Wicara',
                'status' => 'final',
                'locked_at' => $now->copy()->subMinutes(15),
                'signed_by_id' => 14,
                'sha256_hash' => hash('sha256', 'EMR-202611-0001-AZHAR-FINAL-SEALED'),
            ],
            [
                'id' => 2,
                'no_emr' => 'EMR-202611-0002',
                'profile_anak_id' => 6,
                'registrasi_anak_id' => null,
                'terapis_id' => 14,
                'tanggal_pemeriksaan' => $now->copy()->subDays(4)->toDateString(),
                'jenis_layanan' => 'Terapi Wicara',
                'status' => 'final',
                'locked_at' => $now->copy()->subDays(4),
                'signed_by_id' => 14,
                'sha256_hash' => hash('sha256', 'EMR-202611-0002-AZHAR-PREV-SEALED'),
            ],
            [
                'id' => 3,
                'no_emr' => 'EMR-202611-0003',
                'profile_anak_id' => 7,
                'registrasi_anak_id' => 102,
                'terapis_id' => 18,
                'tanggal_pemeriksaan' => $todayStr,
                'jenis_layanan' => 'Sensori Integrasi',
                'status' => 'draft',
                'locked_at' => null,
                'signed_by_id' => null,
                'sha256_hash' => null,
            ],
            [
                'id' => 4,
                'no_emr' => 'EMR-202611-0004',
                'profile_anak_id' => 8,
                'registrasi_anak_id' => null,
                'terapis_id' => 23,
                'tanggal_pemeriksaan' => $now->copy()->subDays(2)->toDateString(),
                'jenis_layanan' => 'Terapi Okupasi & Perilaku',
                'status' => 'amended',
                'locked_at' => $now->copy()->subDays(2),
                'signed_by_id' => 14,
                'sha256_hash' => hash('sha256', 'EMR-202611-0004-HILMI-AMENDED'),
            ],
        ];

        foreach ($emrRecords as $rec) {
            DB::table('emr_records')->updateOrInsert(
                ['id' => $rec['id']],
                array_merge($rec, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // SOAP Detail
        $soaps = [
            [
                'emr_record_id' => 1,
                'subjektif' => "Ibu melaporkan ananda di rumah sudah mulai menirukan suku kata /ba-ba/, /ma-ma/. Saat lapar sudah bisa menunjuk dispenser air minum.",
                'objektif' => "Kontak mata terjalin 4-6 detik saat diajak bermain mobil warna-warni. Artikulasi huruf bilabial /p, b, m/ meningkat. Belum muncul kata bermakna spontan.",
                'asesmen' => "Keterlambatan bicara ekspresif (ICD-10 F80.1). Respons oral-motorik mengalami peningkatan 20% dibanding asesmen awal.",
                'planning' => "Target sesi depan: stimulasi kata bermakna 'mama', 'mau', 'buka'. Latihan pernapasan dan kekuatan diafragma.",
                'target_sesi' => "Menirukan 3 kata 2-suku kata konsonan-vokal.",
                'aktivitas_terapi' => "Meniup peluit kodok, meniup lilin di wadah air, matching kartu flashcard bergambar buah dan hewan.",
                'respons_anak' => "Ananda kooperatif, antusias bermain dengan busa sabun dan flashcard.",
                'rekomendasi_home_program' => "Hindari gadget sama sekali (*zero screen time*). Bacakan buku cerita bergambar 15 menit sebelum tidur dengan intonasi jelas.",
            ],
            [
                'emr_record_id' => 2,
                'subjektif' => "Sesi sebelumnya ananda tampak lelah karena tidur siang terlambat.",
                'objektif' => "Kontak mata singkat 2 detik, sempat menolak duduk di kursi terapi.",
                'asesmen' => "Regulasi emosi masih labil saat lelah.",
                'planning' => "Penguatan bonding dan conditioning ruang terapi.",
                'target_sesi' => "Duduk tenang dan mempertahankan atensi 10 menit.",
                'aktivitas_terapi' => "Bermain playdough sensori dan puzzle kayu sederhana.",
                'respons_anak' => "Setelah 10 menit mulai tenang dan mau mengikuti instruksi terapis.",
                'rekomendasi_home_program' => "Pastikan jam tidur siang konsisten minimal 1.5 jam sebelum sesi terapi.",
            ],
            [
                'emr_record_id' => 3,
                'subjektif' => "Orang tua mengeluhkan anak masih sering jinjit dan menolak jika tangannya kotor.",
                'objektif' => "Sensitivitas taktil tinggi pada tekstur pasir kinetik. Respons vestibular di ayunan hammock baik.",
                'asesmen' => "Gangguan modulasi sensori taktil (Tactile Defensiveness).",
                'planning' => "Protokol sikat Wilbarger dan kompresi sendi secara bertahap.",
                'target_sesi' => "Toleransi menyentuh tekstur tepung dan pasir selama 5 menit tanpa rewel.",
                'aktivitas_terapi' => "Ayunan gantung vestibular, merangkak di terowongan busa, bermain sensory bin jagung dan kacang hijau.",
                'respons_anak' => "Sangat menyukai ayunan, awalnya menolak menyentuh kacang namun perlahan mulai toleran.",
                'rekomendasi_home_program' => "Lakukan pijat kompresi sendi lembut pada lengan dan tungkai sebelum mandi.",
            ],
            [
                'emr_record_id' => 4,
                'subjektif' => "Guru sekolah melaporkan anak sering meninggalkan bangku saat jam pelajaran berlangsung.",
                'objektif' => "Impulsifitas tinggi, atensi selektif mudah terdistraksi suara dari luar ruangan.",
                'asesmen' => "ADHD tipe kombinasi (F90.0).",
                'planning' => "Modifikasi perilaku dengan sistem reward token visual.",
                'target_sesi' => "Duduk fokus menyelesaikan lembar kerja maze selama 15 menit.",
                'aktivitas_terapi' => "Latihan koordinasi mata-tangan dengan papan pasak, permainan Simon Says untuk melatih *inhibitory control*.",
                'respons_anak' => "Mampu menyelesaikan 2 dari 3 lembar kerja dengan bantuan reward stiker bintang.",
                'rekomendasi_home_program' => "Gunakan timer visual saat belajar di rumah (mis. belajar 15 menit, jeda gerak 5 menit).",
            ],
        ];

        foreach ($soaps as $s) {
            DB::table('emr_soaps')->updateOrInsert(
                ['emr_record_id' => $s['emr_record_id']],
                array_merge($s, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // Addendum untuk EMR #4
        DB::table('emr_addendums')->updateOrInsert(
            ['emr_record_id' => 4, 'alasan_koreksi' => 'Koreksi durasi atensi dari 10 menit menjadi 15 menit'],
            [
                'user_id' => 11,
                'alasan_koreksi' => 'Koreksi durasi atensi dari 10 menit menjadi 15 menit setelah verifikasi catatan observasi terapis.',
                'catatan_addendum' => 'Catatan revisi: Pada sesi ke-6 ananda berhasil mempertahankan atensi hingga 15 menit dengan intervensi timer visual.',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // ================= 8. GOAL TRACKING (TARGET CAPAIAN) =================
        $goals = [
            [
                'profile_anak_id' => 6,
                'tipe_goal' => 'jangka_pendek',
                'judul_target' => 'Menirukan 10 Kata Bermakna Konsonan Bilabial (M, P, B)',
                'indikator_keberhasilan' => 'Mampu menirukan kata mama, papa, bola, pipi, buka dengan artikulasi jelas minimal 80% akurasi.',
                'baseline_persen' => 10,
                'capaian_persen' => 65,
                'tanggal_target' => $now->copy()->addMonths(1)->toDateString(),
                'status' => 'aktif',
            ],
            [
                'profile_anak_id' => 6,
                'tipe_goal' => 'jangka_panjang',
                'judul_target' => 'Mampu Berkomunikasi 2 Arah Menggunakan Kalimat 2 Kata (S-P)',
                'indikator_keberhasilan' => 'Mengungkapkan kebutuhan spontan seperti "mau makan", "naik mobil", "buka pintu" tanpa prompting.',
                'baseline_persen' => 0,
                'capaian_persen' => 35,
                'tanggal_target' => $now->copy()->addMonths(6)->toDateString(),
                'status' => 'aktif',
            ],
            [
                'profile_anak_id' => 7,
                'tipe_goal' => 'jangka_pendek',
                'judul_target' => 'Desensitisasi Taktil pada Berbagai Tekstur Makanan & Benda',
                'indikator_keberhasilan' => 'Mampu menyentuh beras, tepung, dan jelly tanpa respon jijik atau menarik tangan.',
                'baseline_persen' => 5,
                'capaian_persen' => 80,
                'tanggal_target' => $now->copy()->addWeeks(3)->toDateString(),
                'status' => 'aktif',
            ],
            [
                'profile_anak_id' => 8,
                'tipe_goal' => 'jangka_pendek',
                'judul_target' => 'Mempertahankan Atensi Terfokus Selama 20 Menit di Kelas',
                'indikator_keberhasilan' => 'Menyelesaikan tugas mewarnai dan menulis tanpa berdiri atau mengganggu teman sekelas.',
                'baseline_persen' => 20,
                'capaian_persen' => 100,
                'tanggal_target' => $now->toDateString(),
                'status' => 'tercapai',
            ],
        ];

        foreach ($goals as $g) {
            DB::table('emr_goal_trackings')->updateOrInsert(
                ['profile_anak_id' => $g['profile_anak_id'], 'judul_target' => $g['judul_target']],
                array_merge($g, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // ================= 9. INVOICES, ITEMS, & TRANSAKSI PEMBAYARAN KASIR =================
        $invoices = [
            [
                'id' => 501,
                'nomor_invoice' => 'INV-' . date('Ymd') . '-0001',
                'profile_anak_id' => 6,
                'registrasi_anak_id' => 101,
                'subtotal' => 350000,
                'diskon' => 25000,
                'total_tagihan' => 325000,
                'total_terbayar' => 325000,
                'sisa_tagihan' => 0,
                'status' => 'lunas',
                'jenis_penjamin' => 'Umum',
                'catatan' => 'Pembayaran lunas via QRIS klinik.',
            ],
            [
                'id' => 502,
                'nomor_invoice' => 'INV-' . date('Ymd') . '-0002',
                'profile_anak_id' => 7,
                'registrasi_anak_id' => 102,
                'subtotal' => 1200000,
                'diskon' => 100000,
                'total_tagihan' => 1100000,
                'total_terbayar' => 500000,
                'sisa_tagihan' => 600000,
                'status' => 'bayar_sebagian',
                'jenis_penjamin' => 'Asuransi Swasta',
                'catatan' => 'DP Paket Sensori Integrasi 8 Sesi.',
            ],
            [
                'id' => 503,
                'nomor_invoice' => 'INV-' . date('Ymd') . '-0003',
                'profile_anak_id' => 8,
                'registrasi_anak_id' => 103,
                'subtotal' => 150000,
                'diskon' => 0,
                'total_tagihan' => 150000,
                'total_terbayar' => 0,
                'sisa_tagihan' => 150000,
                'status' => 'belum_bayar',
                'jenis_penjamin' => 'Umum',
                'catatan' => 'Menunggu verifikasi pembayaran transfer bank.',
            ],
        ];

        foreach ($invoices as $inv) {
            DB::table('invoices')->updateOrInsert(
                ['id' => $inv['id']],
                array_merge($inv, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // Invoice Items
        DB::table('invoice_items')->updateOrInsert(
            ['invoice_id' => 501, 'deskripsi' => 'Sesi Terapi Wicara Intensif (1 Jam)'],
            ['tipe_item' => 'layanan_terapi', 'qty' => 1, 'harga_satuan' => 200000, 'subtotal' => 200000, 'created_at' => $now, 'updated_at' => $now]
        );
        DB::table('invoice_items')->updateOrInsert(
            ['invoice_id' => 501, 'deskripsi' => 'Gym Ball Sensori 65cm (Alat Terapi Rumah)'],
            ['tipe_item' => 'produk_farmasi', 'qty' => 1, 'harga_satuan' => 150000, 'subtotal' => 150000, 'created_at' => $now, 'updated_at' => $now]
        );
        DB::table('invoice_items')->updateOrInsert(
            ['invoice_id' => 502, 'deskripsi' => 'Paket Sensori Integrasi Pediatrik (8 Sesi)'],
            ['tipe_item' => 'paket_terapi', 'qty' => 1, 'harga_satuan' => 1200000, 'subtotal' => 1200000, 'created_at' => $now, 'updated_at' => $now]
        );

        // Transaksi Pembayaran Kasir
        DB::table('pembayaran_transaksi')->updateOrInsert(
            ['invoice_id' => 501, 'nomor_transaksi' => 'TRX-' . date('Ymd') . '-0001'],
            [
                'kasir_id' => 11,
                'metode_bayar' => 'qris',
                'jumlah_bayar' => 325000,
                'kembalian' => 0,
                'nomor_referensi' => 'QRIS-BCA-98762312',
                'status' => 'sukses',
                'catatan' => 'Lunas via QRIS Dinamis kasir.',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // ================= 10. SHIFT KASIR AKTIF =================
        DB::table('shift_kasir')->updateOrInsert(
            ['kasir_id' => 11, 'status' => 'open'],
            [
                'waktu_buka' => $now->copy()->setTime(8, 0, 0),
                'saldo_awal' => 250000,
                'total_tunai_sistem' => 450000,
                'total_non_tunai_sistem' => 825000,
                'saldo_akhir_fisik' => null,
                'selisih' => null,
                'status' => 'open',
                'catatan_shift' => 'Shift Pagi Kasir Utama berjalan lancar.',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // ================= 11. PAKET TERAPI PASIEN DENGAN SISA SESI =================
        $pakets = [
            [
                'profile_anak_id' => 6,
                'layanan_id' => 4,
                'invoice_id' => 501,
                'nomor_paket' => 'PKT-202611-0001',
                'nama_paket' => 'Paket Terapi Wicara & Bahasa (8 Sesi)',
                'total_sesi' => 8,
                'sesi_terpakai' => 3,
                'sisa_sesi' => 5,
                'tanggal_mulai' => $now->copy()->subWeeks(2)->toDateString(),
                'tanggal_berakhir' => $now->copy()->addMonths(3)->toDateString(),
                'status' => 'aktif',
            ],
            [
                'profile_anak_id' => 7,
                'layanan_id' => 4,
                'invoice_id' => 502,
                'nomor_paket' => 'PKT-202611-0002',
                'nama_paket' => 'Paket Sensori Integrasi & Okupasi (12 Sesi)',
                'total_sesi' => 12,
                'sesi_terpakai' => 4,
                'sisa_sesi' => 8,
                'tanggal_mulai' => $now->copy()->subWeeks(3)->toDateString(),
                'tanggal_berakhir' => $now->copy()->addMonths(5)->toDateString(),
                'status' => 'aktif',
            ]
        ];

        foreach ($pakets as $pkt) {
            DB::table('paket_terapi_pasien')->updateOrInsert(
                ['nomor_paket' => $pkt['nomor_paket']],
                array_merge($pkt, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // ================= 12. ARSIP SURAT RESMI KLINIK & QR VERIFIKASI =================
        $surats = [
            [
                'nomor_surat' => '001/KLINIK-SKT/XI/2026',
                'surat_template_id' => 1,
                'profile_anak_id' => 6,
                'penandatangan_id' => 14,
                'judul_surat' => 'Surat Keterangan Sedang Menjalani Terapi',
                'parameter_data' => json_encode(['diagnosis' => 'Speech Delay', 'jadwal' => 'Senin & Kamis']),
                'isi_final_html' => "<p>Yang bertanda tangan di bawah ini menerangkan bahwa ananda <strong>Azhar Al-Farizi</strong> (No. RM: RM-202512-00006) benar saat ini sedang aktif mengikuti program pelayanan <strong>Terapi Wicara</strong> di klinik kami secara rutin 2 kali seminggu.</p>",
                'qr_verification_code' => 'QR-SKT-202611-AZHAR-VERIFIED',
                'status' => 'final',
                'tanggal_surat' => $now->copy()->subDays(3)->toDateString(),
                'created_by' => 11,
            ],
            [
                'nomor_surat' => '002/KLINIK-REK/XI/2026',
                'surat_template_id' => 2,
                'profile_anak_id' => 8,
                'penandatangan_id' => 18,
                'judul_surat' => 'Surat Rekomendasi Akomodasi Belajar di Sekolah',
                'parameter_data' => json_encode(['sekolah' => 'SD Inklusi Permata Hati']),
                'isi_final_html' => "<p>Kepada Pihak Sekolah, kami merekomendasikan ananda <strong>Hilmi Ramadhan</strong> (No. RM: RM-202512-00008) diberikan tempat duduk di barisan depan serta jeda peregangan gerak setiap 20 menit guna mendukung regulasi atensi.</p>",
                'qr_verification_code' => 'QR-REK-202611-HILMI-VERIFIED',
                'status' => 'final',
                'tanggal_surat' => $now->copy()->subDays(1)->toDateString(),
                'created_by' => 11,
            ]
        ];

        foreach ($surats as $srt) {
            DB::table('surat_keluar')->updateOrInsert(
                ['nomor_surat' => $srt['nomor_surat']],
                array_merge($srt, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // ================= 13. MASTER PRODUK FARMASI & LOGISTIK DENGAN BATCH (FEFO) =================
        $products = [
            ['kode_produk' => 'BHP-001', 'nama_produk' => 'Hand Sanitizer Gel 500ml', 'jenis' => 'bhp', 'satuan' => 'botol', 'harga_beli' => 35000, 'harga_jual' => 45000, 'total_stok' => 25, 'stok_minimum' => 5],
            ['kode_produk' => 'BHP-002', 'nama_produk' => 'Masker Bedah Anak 3-Ply (Isi 50)', 'jenis' => 'bhp', 'satuan' => 'box', 'harga_beli' => 28000, 'harga_jual' => 38000, 'total_stok' => 18, 'stok_minimum' => 5],
            ['kode_produk' => 'BHP-003', 'nama_produk' => 'Sarung Tangan Nitrile Non-Latex (M)', 'jenis' => 'bhp', 'satuan' => 'box', 'harga_beli' => 65000, 'harga_jual' => 85000, 'total_stok' => 3, 'stok_minimum' => 5], // Kritis!
            ['kode_produk' => 'ALAT-001', 'nama_produk' => 'Gym Ball Sensori 65cm Anti-Burst', 'jenis' => 'alat_terapi', 'satuan' => 'pcs', 'harga_beli' => 120000, 'harga_jual' => 165000, 'total_stok' => 12, 'stok_minimum' => 3],
            ['kode_produk' => 'ALAT-002', 'nama_produk' => 'Weighted Vest / Rompi Beban Anak 2kg', 'jenis' => 'alat_terapi', 'satuan' => 'pcs', 'harga_beli' => 250000, 'harga_jual' => 320000, 'total_stok' => 4, 'stok_minimum' => 2],
            ['kode_produk' => 'ALAT-003', 'nama_produk' => 'Chewy Tube Oral-Motor P-Shape', 'jenis' => 'alat_terapi', 'satuan' => 'pcs', 'harga_beli' => 45000, 'harga_jual' => 65000, 'total_stok' => 15, 'stok_minimum' => 4],
            ['kode_produk' => 'MAINAN-001', 'nama_produk' => 'Pasir Kinetik Sensori 1kg + Cetakan', 'jenis' => 'mainan_edukasi', 'satuan' => 'set', 'harga_beli' => 48000, 'harga_jual' => 70000, 'total_stok' => 10, 'stok_minimum' => 3],
            ['kode_produk' => 'MAINAN-002', 'nama_produk' => 'Balok Susun Geometri Kayu Montessori', 'jenis' => 'mainan_edukasi', 'satuan' => 'set', 'harga_beli' => 75000, 'harga_jual' => 110000, 'total_stok' => 8, 'stok_minimum' => 2],
            ['kode_produk' => 'MAINAN-003', 'nama_produk' => 'Flash Card Kosakata Bicara (100 Kartu)', 'jenis' => 'mainan_edukasi', 'satuan' => 'box', 'harga_beli' => 32000, 'harga_jual' => 50000, 'total_stok' => 20, 'stok_minimum' => 5],
            ['kode_produk' => 'SUPLEMEN-001', 'nama_produk' => 'Curcuma Plus Sirup Rasa Jeruk 200ml', 'jenis' => 'suplemen', 'satuan' => 'botol', 'harga_beli' => 22000, 'harga_jual' => 30000, 'total_stok' => 14, 'stok_minimum' => 4],
        ];

        foreach ($products as $p) {
            DB::table('master_produk')->updateOrInsert(
                ['kode_produk' => $p['kode_produk']],
                array_merge($p, ['status_aktif' => true, 'created_at' => $now, 'updated_at' => $now])
            );
            $pId = DB::table('master_produk')->where('kode_produk', $p['kode_produk'])->value('id');

            // Tambahkan batch FEFO untuk barang
            DB::table('produk_batch')->updateOrInsert(
                ['master_produk_id' => $pId, 'nomor_batch' => 'BATCH-' . substr($p['kode_produk'], 0, 3) . '-2026A'],
                [
                    'tanggal_kedaluwarsa' => $now->copy()->addMonths(rand(6, 18))->toDateString(),
                    'stok' => $p['total_stok'],
                    'harga_beli_satuan' => $p['harga_beli'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            // Kartu stok awal
            DB::table('kartu_stok')->updateOrInsert(
                ['master_produk_id' => $pId, 'referensi' => 'Saldo Awal Sistem'],
                [
                    'tanggal' => $now->copy()->subDays(5),
                    'tipe_mutasi' => 'masuk_po',
                    'jumlah' => $p['total_stok'],
                    'stok_sebelum' => 0,
                    'stok_sesudah' => $p['total_stok'],
                    'user_id' => 11,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // ================= 14. LOG AUDIT MEDIS =================
        $audits = [
            ['user_id' => 11, 'profile_anak_id' => 6, 'action' => 'VIEW_TIMELINE', 'alasan_akses' => 'Pemeriksaan rutin rekam medis', 'created_at' => $now->copy()->subHours(3)],
            ['user_id' => 11, 'profile_anak_id' => 6, 'action' => 'LOCK_AND_SIGN', 'alasan_akses' => 'Finalisasi EMR Sesi Terapi Wicara', 'created_at' => $now->copy()->subMinutes(15)],
            ['user_id' => 11, 'profile_anak_id' => 7, 'action' => 'CREATE_SOAP', 'alasan_akses' => 'Pencatatan sesi sensori integrasi', 'created_at' => $now->copy()->subMinutes(45)],
            ['user_id' => 11, 'profile_anak_id' => 8, 'action' => 'ADDENDUM', 'alasan_akses' => 'Koreksi durasi atensi ananda di kelas', 'created_at' => $now->copy()->subDays(2)],
        ];

        foreach ($audits as $ad) {
            DB::table('emr_audit_logs')->insert(
                array_merge($ad, ['ip_address' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'])
            );
        }
    }
}
