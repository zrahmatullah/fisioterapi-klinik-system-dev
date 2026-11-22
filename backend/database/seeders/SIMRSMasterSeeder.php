<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SIMRSMasterSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // ================= 1. ROLES =================
        $roles = [
            ['id' => 13, 'role' => 'Admin', 'status_aktif' => true],
            ['id' => 14, 'role' => 'Terapis', 'status_aktif' => true],
            ['id' => 15, 'role' => 'Orang Tua', 'status_aktif' => true],
            ['id' => 16, 'role' => 'Pendaftaran', 'status_aktif' => true],
            ['id' => 17, 'role' => 'Dokter/Psikolog', 'status_aktif' => true],
            ['id' => 18, 'role' => 'Apoteker', 'status_aktif' => true],
            ['id' => 19, 'role' => 'Kasir', 'status_aktif' => true],
            ['id' => 20, 'role' => 'Gudang/Logistik', 'status_aktif' => true],
            ['id' => 21, 'role' => 'Manajemen/Owner', 'status_aktif' => true],
        ];

        foreach ($roles as $r) {
            DB::table('role')->updateOrInsert(
                ['id' => $r['id']],
                [
                    'role' => $r['role'],
                    'status_aktif' => $r['status_aktif'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // ================= 2. PERMISSIONS =================
        $permissions = [
            // Pendaftaran & Pasien
            ['name' => 'pasien.view', 'group' => 'Pasien', 'description' => 'Melihat data pasien'],
            ['name' => 'pasien.create', 'group' => 'Pasien', 'description' => 'Menambah data anak/pasien'],
            ['name' => 'pasien.update', 'group' => 'Pasien', 'description' => 'Mengubah data pasien'],
            ['name' => 'pasien.merge', 'group' => 'Pasien', 'description' => 'Menggabungkan data pasien ganda'],
            ['name' => 'registrasi.create', 'group' => 'Pendaftaran', 'description' => 'Membuat registrasi kunjungan'],
            ['name' => 'antrian.call', 'group' => 'Pendaftaran', 'description' => 'Memanggil nomor antrian'],

            // EMR
            ['name' => 'emr.view', 'group' => 'EMR', 'description' => 'Membuka timeline dan rekam medis pasien'],
            ['name' => 'emr.create', 'group' => 'EMR', 'description' => 'Membuat asesmen dan catatan SOAP'],
            ['name' => 'emr.sign', 'group' => 'EMR', 'description' => 'Menandatangani & mengunci rekam medis (Final)'],
            ['name' => 'emr.addendum', 'group' => 'EMR', 'description' => 'Membuat addendum pada rekam medis final'],
            ['name' => 'emr.export', 'group' => 'EMR', 'description' => 'Mengunduh resume medis PDF'],

            // Kasir & Billing
            ['name' => 'billing.view', 'group' => 'Kasir', 'description' => 'Melihat daftar invoice dan pembayaran'],
            ['name' => 'billing.create', 'group' => 'Kasir', 'description' => 'Menerbitkan tagihan dan menerima pembayaran'],
            ['name' => 'billing.shift', 'group' => 'Kasir', 'description' => 'Membuka dan menutup shift kasir'],

            // Surat & Dokumen
            ['name' => 'surat.view', 'group' => 'Surat', 'description' => 'Melihat arsip surat'],
            ['name' => 'surat.create', 'group' => 'Surat', 'description' => 'Membuat surat dari template'],
            ['name' => 'surat.template', 'group' => 'Surat', 'description' => 'Mengelola template surat'],

            // Farmasi & Logistik
            ['name' => 'farmasi.view', 'group' => 'Farmasi', 'description' => 'Melihat produk farmasi dan stok'],
            ['name' => 'farmasi.manage', 'group' => 'Farmasi', 'description' => 'Mengelola produk obat dan batch FEFO'],
            ['name' => 'logistik.manage', 'group' => 'Logistik', 'description' => 'Mengelola PO, mutasi, dan stok opname'],

            // Laporan & Admin
            ['name' => 'laporan.view', 'group' => 'Laporan', 'description' => 'Melihat laporan dan analitik klinik'],
            ['name' => 'settings.manage', 'group' => 'Pengaturan', 'description' => 'Mengelola pengaturan profil klinik dan RBAC'],
            ['name' => 'audit.view', 'group' => 'Pengaturan', 'description' => 'Melihat jejak audit sistem & EMR'],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $p['name']],
                [
                    'group' => $p['group'],
                    'description' => $p['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // Assign All Permissions to Admin
        $allPermIds = DB::table('permissions')->pluck('id')->toArray();
        foreach ($allPermIds as $pId) {
            DB::table('role_permissions')->updateOrInsert(
                ['role_id' => 13, 'permission_id' => $pId],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        // ================= 3. MASTER ICD-10 =================
        $icd10Data = [
            ['kode' => 'F80.0', 'deskripsi_en' => 'Specific speech articulation disorder', 'deskripsi_id' => 'Gangguan artikulasi bicara khas', 'kategori' => 'Gangguan Wicara', 'is_terapi_anak' => true],
            ['kode' => 'F80.1', 'deskripsi_en' => 'Expressive language disorder', 'deskripsi_id' => 'Gangguan berbahasa ekspresif (Speech Delay)', 'kategori' => 'Gangguan Wicara', 'is_terapi_anak' => true],
            ['kode' => 'F80.2', 'deskripsi_en' => 'Receptive language disorder', 'deskripsi_id' => 'Gangguan berbahasa reseptif', 'kategori' => 'Gangguan Wicara', 'is_terapi_anak' => true],
            ['kode' => 'F84.0', 'deskripsi_en' => 'Childhood autism', 'deskripsi_id' => 'Autisme masa kanak-kanak (ASD)', 'kategori' => 'Perkembangan Pervasif', 'is_terapi_anak' => true],
            ['kode' => 'F84.5', 'deskripsi_en' => 'Asperger syndrome', 'deskripsi_id' => 'Sindrom Asperger', 'kategori' => 'Perkembangan Pervasif', 'is_terapi_anak' => true],
            ['kode' => 'F90.0', 'deskripsi_en' => 'Disturbance of activity and attention', 'deskripsi_id' => 'Gangguan aktivitas dan perhatian (ADHD/GPPH)', 'kategori' => 'Perilaku & Emosional', 'is_terapi_anak' => true],
            ['kode' => 'F82', 'deskripsi_en' => 'Specific developmental disorder of motor function', 'deskripsi_id' => 'Gangguan perkembangan fungsi motorik khas (Dispraksia)', 'kategori' => 'Motorik', 'is_terapi_anak' => true],
            ['kode' => 'G80.0', 'deskripsi_en' => 'Spastic cerebral palsy', 'deskripsi_id' => 'Cerebral Palsy Spastik', 'kategori' => 'Neuromuskular', 'is_terapi_anak' => true],
            ['kode' => 'R62.5', 'deskripsi_en' => 'Other lack of expected normal physiological development', 'deskripsi_id' => 'Keterlambatan perkembangan fisiologis (Global Delay)', 'kategori' => 'Tumbuh Kembang', 'is_terapi_anak' => true],
            ['kode' => 'F88', 'deskripsi_en' => 'Other disorders of psychological development', 'deskripsi_id' => 'Gangguan perkembangan psikologis lain (Sensory Processing)', 'kategori' => 'Sensori Integrasi', 'is_terapi_anak' => true],
            ['kode' => 'Z00.1', 'deskripsi_en' => 'Routine child health examination', 'deskripsi_id' => 'Pemeriksaan rutin kesehatan dan tumbuh kembang anak', 'kategori' => 'Pemeriksaan Umum', 'is_terapi_anak' => true],
        ];

        foreach ($icd10Data as $icd) {
            DB::table('master_icd10')->updateOrInsert(
                ['kode' => $icd['kode']],
                array_merge($icd, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // ================= 4. MASTER ICD-9-CM =================
        $icd9Data = [
            ['kode' => '93.75', 'deskripsi_en' => 'Speech training and speech therapy', 'deskripsi_id' => 'Terapi wicara dan pelatihan bahasa', 'is_terapi_anak' => true],
            ['kode' => '93.83', 'deskripsi_en' => 'Occupational therapy', 'deskripsi_id' => 'Terapi okupasi dan sensori integrasi', 'is_terapi_anak' => true],
            ['kode' => '93.11', 'deskripsi_en' => 'Assisting exercise', 'deskripsi_id' => 'Fisioterapi pediatrik dan latihan gerak', 'is_terapi_anak' => true],
            ['kode' => '94.31', 'deskripsi_en' => 'Psychoanalysis / child behavioral therapy', 'deskripsi_id' => 'Terapi perilaku (ABA) / Psikoterapi anak', 'is_terapi_anak' => true],
            ['kode' => '93.05', 'deskripsi_en' => 'Range of motion testing / development assessment', 'deskripsi_id' => 'Asesmen awal tumbuh kembang & milestone', 'is_terapi_anak' => true],
        ];

        foreach ($icd9Data as $icd9) {
            DB::table('master_icd9')->updateOrInsert(
                ['kode' => $icd9['kode']],
                array_merge($icd9, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // ================= 5. CLINIC SETTINGS =================
        $settings = [
            ['key' => 'clinic_name', 'value' => 'Klinik Tumbuh Kembang & Terapi Anak E-Clinic', 'group' => 'general', 'description' => 'Nama Resmi Klinik'],
            ['key' => 'clinic_address', 'value' => 'Jl. Kesehatan Anak No. 123, Jakarta Selatan', 'group' => 'general', 'description' => 'Alamat Klinik'],
            ['key' => 'clinic_phone', 'value' => '021-7890123 / 081234567890', 'group' => 'general', 'description' => 'Nomor Kontak'],
            ['key' => 'clinic_email', 'value' => 'info@klinikanak.id', 'group' => 'general', 'description' => 'Email Resmi'],
            ['key' => 'clinic_sip_izin', 'value' => '445/098/SIP-KLINIK/2026', 'group' => 'general', 'description' => 'Nomor Izin Operasional Klinik'],
            ['key' => 'format_no_rm', 'value' => 'RM-{YEAR}{MONTH}-{SEQ}', 'group' => 'pendaftaran', 'description' => 'Format Nomor Rekam Medis'],
            ['key' => 'format_no_regis', 'value' => 'REG-{YEAR}{MONTH}{DAY}-{SEQ}', 'group' => 'pendaftaran', 'description' => 'Format Nomor Registrasi'],
            ['key' => 'format_no_antrian', 'value' => 'A-{SEQ}', 'group' => 'pendaftaran', 'description' => 'Format Nomor Antrian'],
            ['key' => 'farmasi_enabled', 'value' => '1', 'group' => 'features', 'description' => 'Status Modul Farmasi & BHP (1=Aktif, 0=Nonaktif)'],
            ['key' => 'satusehat_mode', 'value' => 'sandbox', 'group' => 'integration', 'description' => 'Mode SATUSEHAT (sandbox / production / mock)'],
        ];

        foreach ($settings as $s) {
            DB::table('clinic_settings')->updateOrInsert(
                ['key' => $s['key']],
                array_merge($s, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // ================= 6. SURAT TEMPLATES =================
        $templates = [
            [
                'kode_template' => 'SK-TERAPI',
                'judul' => 'Surat Keterangan Sedang Menjalani Terapi',
                'jenis_surat' => 'keterangan_terapi',
                'format_nomor' => '{SEQ}/KLINIK-SKT/{ROMAN_MONTH}/{YEAR}',
                'template_content' => '<p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
<table>
  <tr><td>Nama Anak</td><td>: <strong>{{nama_pasien}}</strong></td></tr>
  <tr><td>No. Rekam Medis</td><td>: {{no_rm}}</td></tr>
  <tr><td>Tanggal Lahir / Usia</td><td>: {{tanggal_lahir}} ({{usia}})</td></tr>
  <tr><td>Nama Orang Tua</td><td>: {{nama_orang_tua}}</td></tr>
  <tr><td>Diagnosis Klinis</td><td>: {{diagnosis}}</td></tr>
</table>
<p>Benar saat ini sedang aktif mengikuti program pelayanan <strong>{{jenis_terapi}}</strong> di klinik kami dengan jadwal rutin {{jadwal_sesi}}.</p>
<p>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>',
            ],
            [
                'kode_template' => 'REKOM-SEKOLAH',
                'judul' => 'Surat Rekomendasi & Laporan Perkembangan untuk Sekolah',
                'jenis_surat' => 'rekomendasi_sekolah',
                'format_nomor' => '{SEQ}/KLINIK-REK/{ROMAN_MONTH}/{YEAR}',
                'template_content' => '<p>Kepada Yth. Pimpinan/Guru Sekolah {{nama_sekolah}},</p>
<p>Berdasarkan evaluasi terapi berkala ananda <strong>{{nama_pasien}}</strong> (No. RM: {{no_rm}}), berikut kami sampaikan rekomendasi akomodasi pembelajaran di kelas:</p>
<ol>
  <li>Penguatan instruksi visual dan verbal satu per satu.</li>
  <li>Pemberian jeda sensori (*sensory break*) setiap 20-30 menit.</li>
  <li>Dukungan tempat duduk di barisan depan untuk memfasilitasi kontak mata dan fokus.</li>
</ol>
<p>Klinik kami siap berkolaborasi dengan pihak sekolah demi perkembangan optimal ananda.</p>',
            ],
            [
                'kode_template' => 'RUJUKAN-KELUAR',
                'judul' => 'Surat Rujukan Keluar Medis',
                'jenis_surat' => 'rujukan_keluar',
                'format_nomor' => '{SEQ}/KLINIK-RUJ/{ROMAN_MONTH}/{YEAR}',
                'template_content' => '<p>Kepada Yth. Dokter Spesialis Anak / Neurolog / THT di {{tujuan_rujukan}},</p>
<p>Mohon pemeriksaan dan tatalaksana lebih lanjut terhadap pasien:</p>
<table>
  <tr><td>Nama</td><td>: {{nama_pasien}}</td></tr>
  <tr><td>No. RM</td><td>: {{no_rm}}</td></tr>
  <tr><td>Usia</td><td>: {{usia}}</td></tr>
  <tr><td>Indikasi Rujukan</td><td>: {{alasan_rujukan}}</td></tr>
</table>
<p>Atas kerjasama dan bantuan sejawat kami ucapkan terima kasih.</p>',
            ],
        ];

        foreach ($templates as $tmpl) {
            DB::table('surat_templates')->updateOrInsert(
                ['kode_template' => $tmpl['kode_template']],
                array_merge($tmpl, ['status_aktif' => true, 'created_at' => $now, 'updated_at' => $now])
            );
        }

        // ================= 7. KATEGORI & MASTER PRODUK AWAL =================
        $kategoriProduk = [
            ['kode_kategori' => 'BHP', 'nama_kategori' => 'Bahan Habis Pakai Medis', 'jenis' => 'medis'],
            ['kode_kategori' => 'ALAT', 'nama_kategori' => 'Alat & Instrumen Terapi', 'jenis' => 'non_medis'],
            ['kode_kategori' => 'MAINAN', 'nama_kategori' => 'Mainan Edukasi & Sensori', 'jenis' => 'non_medis'],
            ['kode_kategori' => 'SUPLEMEN', 'nama_kategori' => 'Suplemen & Vitamin Anak', 'jenis' => 'medis'],
        ];

        foreach ($kategoriProduk as $kp) {
            DB::table('produk_kategori')->updateOrInsert(
                ['kode_kategori' => $kp['kode_kategori']],
                array_merge($kp, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        $suppId = DB::table('supplier')->updateOrInsert(
            ['nama_supplier' => 'PT Mitra Edukasi & Medika Sejahtera'],
            [
                'kontak_person' => 'Budi Santoso',
                'no_telp' => '08119876543',
                'alamat' => 'Kawasan Industri Pulogadung, Jakarta',
                'status_aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $katBhpId = DB::table('produk_kategori')->where('kode_kategori', 'BHP')->value('id');
        $katAlatId = DB::table('produk_kategori')->where('kode_kategori', 'ALAT')->value('id');

        $starterProducts = [
            [
                'kode_produk' => 'BHP-001',
                'nama_produk' => 'Hand Sanitizer Gel 500ml',
                'produk_kategori_id' => $katBhpId,
                'jenis' => 'bhp',
                'satuan' => 'botol',
                'harga_beli' => 35000,
                'harga_jual' => 45000,
                'total_stok' => 20,
                'stok_minimum' => 5,
            ],
            [
                'kode_produk' => 'ALAT-001',
                'nama_produk' => 'Gym Ball Sensori 65cm',
                'produk_kategori_id' => $katAlatId,
                'jenis' => 'alat_terapi',
                'satuan' => 'pcs',
                'harga_beli' => 120000,
                'harga_jual' => 175000,
                'total_stok' => 8,
                'stok_minimum' => 2,
            ],
            [
                'kode_produk' => 'MAINAN-001',
                'nama_produk' => 'Balok Susun Geometri Kayu',
                'produk_kategori_id' => $katAlatId,
                'jenis' => 'mainan_edukasi',
                'satuan' => 'set',
                'harga_beli' => 75000,
                'harga_jual' => 110000,
                'total_stok' => 15,
                'stok_minimum' => 3,
            ]
        ];

        foreach ($starterProducts as $prod) {
            DB::table('master_produk')->updateOrInsert(
                ['kode_produk' => $prod['kode_produk']],
                array_merge($prod, ['status_aktif' => true, 'created_at' => $now, 'updated_at' => $now])
            );
        }
    }
}
