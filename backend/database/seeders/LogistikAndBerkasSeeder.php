<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterProduk;
use App\Models\ProdukBatch;
use App\Models\KartuStok;
use App\Models\Supplier;
use App\Models\PurchaseRequisition;
use App\Models\PrItem;
use App\Models\PurchaseOrder;
use App\Models\PoItem;
use App\Models\GoodsReceipt;
use App\Models\GrnItem;
use App\Models\ProfileAnak;
use App\Models\InformedConsent;
use App\Models\SuratKeluar;
use App\Models\SuratTemplate;
use App\Models\EmrAttachment;
use App\Models\Role;
use App\Models\RoleModulePermission;
use Illuminate\Support\Str;

class LogistikAndBerkasSeeder extends Seeder
{
    public function run(): void
    {
        // ================= 1. MASTER SUPPLIER =================
        $suppliers = [
            [
                'nama_supplier' => 'PT Medika Pediatrik Sejahtera',
                'kontak_person' => 'Bpk. Bambang Irawan',
                'no_telp' => '0812-8877-6655',
                'email' => 'sales@medikapediatrik.co.id',
                'alamat' => 'Kawasan Industri Pulogadung Blok B-12, Jakarta Timur',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'PT Terapi Sensori Mandiri',
                'kontak_person' => 'Ibu Ratna Dewi',
                'no_telp' => '0813-1122-3344',
                'email' => 'order@terapisensori.com',
                'alamat' => 'Jl. RS Fatmawati No. 88, Jakarta Selatan',
                'status_aktif' => true,
            ],
            [
                'nama_supplier' => 'PT Kimia Farma Trading & Distribution',
                'kontak_person' => 'Bpk. Hendro',
                'no_telp' => '021-3847709',
                'email' => 'kftd.jakarta@kimiafarma.co.id',
                'alamat' => 'Jl. Budi Utomo No. 1, Jakarta Pusat',
                'status_aktif' => true,
            ],
        ];

        $savedSuppliers = [];
        foreach ($suppliers as $s) {
            $savedSuppliers[] = Supplier::firstOrCreate(['nama_supplier' => $s['nama_supplier']], $s);
        }

        // ================= 2. MASTER PRODUK & STOK AWAL =================
        $produks = [
            [
                'kode_produk' => 'BHP-001',
                'nama_produk' => 'Hand Sanitizer Gel 500ml Medical Grade',
                'jenis' => 'bhp',
                'satuan' => 'botol',
                'harga_beli' => 35000,
                'harga_jual' => 45000,
                'total_stok' => 30,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'BHP-002',
                'nama_produk' => 'Masker Bedah Anak 3-Ply Soft Earloop',
                'jenis' => 'bhp',
                'satuan' => 'box',
                'harga_beli' => 28000,
                'harga_jual' => 38000,
                'total_stok' => 25,
                'stok_minimum' => 10,
            ],
            [
                'kode_produk' => 'ALAT-001',
                'nama_produk' => 'Gym Ball Sensori 65cm Anti-Burst',
                'jenis' => 'alat_terapi',
                'satuan' => 'unit',
                'harga_beli' => 120000,
                'harga_jual' => 165000,
                'total_stok' => 8,
                'stok_minimum' => 4,
            ],
            [
                'kode_produk' => 'ALAT-002',
                'nama_produk' => 'Weighted Blanket / Selimut Beban Sensori 3kg',
                'jenis' => 'alat_terapi',
                'satuan' => 'unit',
                'harga_beli' => 350000,
                'harga_jual' => 475000,
                'total_stok' => 5,
                'stok_minimum' => 2,
            ],
            [
                'kode_produk' => 'EDUKASI-001',
                'nama_produk' => 'Flashcard Artikulasi Huruf R-S-L-K Wicara',
                'jenis' => 'mainan_edukasi',
                'satuan' => 'set',
                'harga_beli' => 85000,
                'harga_jual' => 120000,
                'total_stok' => 12,
                'stok_minimum' => 5,
            ],
        ];

        $savedProduks = [];
        foreach ($produks as $p) {
            $prod = MasterProduk::firstOrCreate(['kode_produk' => $p['kode_produk']], $p);
            $savedProduks[] = $prod;

            // Pastikan ada batch FEFO & kartu stok
            $batch = ProdukBatch::firstOrCreate(
                [
                    'master_produk_id' => $prod->id,
                    'nomor_batch' => 'BATCH-INIT-' . $prod->id,
                ],
                [
                    'tanggal_kedaluwarsa' => now()->addMonths(18)->toDateString(),
                    'stok' => $prod->total_stok,
                    'harga_beli_satuan' => $prod->harga_beli,
                ]
            );

            KartuStok::firstOrCreate(
                [
                    'master_produk_id' => $prod->id,
                    'referensi' => "Saldo Awal Sistem: {$prod->kode_produk}",
                ],
                [
                    'produk_batch_id' => $batch->id,
                    'tanggal' => now()->subDays(5),
                    'tipe_mutasi' => 'saldo_awal',
                    'jumlah' => $prod->total_stok,
                    'stok_sebelum' => 0,
                    'stok_sesudah' => $prod->total_stok,
                    'user_id' => 1,
                ]
            );
        }

        // ================= 3. SEED ALUR LOGISTIK: PR -> PO -> GRN =================
        // PR 1: Disetujui
        $pr1 = PurchaseRequisition::firstOrCreate(
            ['nomor_pr' => 'PR-20261120-0001'],
            [
                'unit_pemohon' => 'Ruang Terapi Sensori Integrasi',
                'tanggal_pr' => now()->subDays(3)->toDateString(),
                'alasan_kebutuhan' => 'Kebutuhan perlengkapan stimulasi taktil & vestibular bulan November',
                'status' => 'diproses_po',
                'created_by' => 1,
                'approved_by' => 1,
                'approved_at' => now()->subDays(2),
                'catatan_approval' => 'Disetujui untuk pengadaan segera',
            ]
        );
        PrItem::firstOrCreate(
            ['purchase_requisition_id' => $pr1->id, 'master_produk_id' => $savedProduks[2]->id],
            ['qty_diminta' => 5, 'qty_disetujui' => 5, 'keterangan' => 'Gym ball tambahan']
        );

        // PR 2: Diajukan
        $pr2 = PurchaseRequisition::firstOrCreate(
            ['nomor_pr' => 'PR-20261121-0002'],
            [
                'unit_pemohon' => 'Poli Terapi Wicara & Oral Motor',
                'tanggal_pr' => now()->subDays(1)->toDateString(),
                'alasan_kebutuhan' => 'Penggantian kartu edukasi artikulasi yang rusak dan alat tiup wicara',
                'status' => 'diajukan',
                'created_by' => 1,
            ]
        );
        PrItem::firstOrCreate(
            ['purchase_requisition_id' => $pr2->id, 'master_produk_id' => $savedProduks[4]->id],
            ['qty_diminta' => 6, 'qty_disetujui' => 6, 'keterangan' => 'Flashcard artikulasi']
        );

        // PO 1: Diterbitkan dari PR 1
        $po1 = PurchaseOrder::firstOrCreate(
            ['nomor_po' => 'PO-20261120-0001'],
            [
                'purchase_requisition_id' => $pr1->id,
                'supplier_id' => $savedSuppliers[1]->id,
                'tanggal_po' => now()->subDays(2)->toDateString(),
                'total_estimasi' => 600000,
                'status' => 'selesai',
                'catatan' => 'Pengiriman langsung ke gudang logistik klinik lantai 1',
                'created_by' => 1,
            ]
        );
        $poItem1 = PoItem::firstOrCreate(
            ['purchase_order_id' => $po1->id, 'master_produk_id' => $savedProduks[2]->id],
            [
                'qty_pesan' => 5,
                'qty_diterima' => 5,
                'harga_satuan' => 120000,
                'subtotal' => 600000,
            ]
        );

        // GRN 1: Penerimaan fisik dari PO 1
        $grn1 = GoodsReceipt::firstOrCreate(
            ['nomor_grn' => 'GRN-20261121-0001'],
            [
                'purchase_order_id' => $po1->id,
                'supplier_id' => $savedSuppliers[1]->id,
                'nomor_surat_jalan' => 'SJ-TSM-2026-9901',
                'nomor_faktur_supplier' => 'INV-TSM-4412',
                'tanggal_terima' => now()->subDays(1)->toDateString(),
                'penerima_id' => 1,
                'status' => 'diterima',
                'total_nilai_barang' => 600000,
                'catatan' => 'Kemasan utuh, kardus tersegel baik dengan garansi pabrik',
            ]
        );
        GrnItem::firstOrCreate(
            ['goods_receipt_id' => $grn1->id, 'master_produk_id' => $savedProduks[2]->id],
            [
                'po_item_id' => $poItem1->id,
                'nomor_batch' => 'BATCH-TSM-2026-11',
                'tanggal_kedaluwarsa' => now()->addYears(3)->toDateString(),
                'qty_diterima' => 5,
                'harga_beli_satuan' => 120000,
                'subtotal' => 600000,
            ]
        );

        // ================= 4. SEED BERKAS-BERKAS PASIEN =================
        $children = ProfileAnak::limit(5)->get();
        foreach ($children as $idx => $child) {
            // 4a. Informed Consent Digital
            InformedConsent::firstOrCreate(
                [
                    'profile_anak_id' => $child->id,
                    'judul_persetujuan' => 'Persetujuan Tindakan Asesmen & Rencana Program Terapi',
                ],
                [
                    'tipe_persetujuan' => 'tindakan_terapi',
                    'pernyataan' => 'Selaku orang tua/wali sah, saya menyatakan setuju atas tindakan evaluasi klinis dan program terapi okupasi/wicara serta pencatatan RME terenkripsi sesuai UU PDP.',
                    'nama_penandatangan' => $child->nama_ibu ?? $child->nama_ayah ?? 'Ahmad Fauzi',
                    'hubungan_wali' => !empty($child->nama_ibu) ? 'Ibu Kandung' : 'Ayah Kandung',
                    'tanda_tangan_base64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
                    'ip_address' => '127.0.0.1',
                    'signed_at' => now()->subDays(7 - $idx),
                ]
            );

            // 4b. Surat Keluar Resmi dengan QR
            $template = SuratTemplate::first();
            if ($template) {
                SuratKeluar::firstOrCreate(
                    [
                        'profile_anak_id' => $child->id,
                        'judul_surat' => 'Surat Keterangan Hasil Evaluasi Klinis Terapi Anak',
                    ],
                    [
                        'nomor_surat' => "SKD/202611/" . str_pad($child->id, 4, '0', STR_PAD_LEFT),
                        'surat_template_id' => $template->id,
                        'penandatangan_id' => 1,
                        'parameter_data' => ['nama' => $child->nama_anak, 'no_rm' => $child->no_rm],
                        'isi_final_html' => "<p>Surat keterangan resmi dokter spesialis anak tumbuh kembang untuk {$child->nama_anak}.</p>",
                        'qr_verification_code' => Str::random(32),
                        'status' => 'final',
                        'tanggal_surat' => now()->subDays(5)->toDateString(),
                        'created_by' => 1,
                    ]
                );
            }

            // 4c. Lampiran Berkas Klinis (Hasil Lab / BERA / Psikotes)
            EmrAttachment::firstOrCreate(
                [
                    'profile_anak_id' => $child->id,
                    'nama_file' => "Hasil_Tes_BERA_Audiometri_{$child->nama_anak}.pdf",
                ],
                [
                    'file_path' => "/uploads/berkas/bera-{$child->id}.pdf",
                    'file_type' => 'application/pdf',
                    'file_size' => 312000,
                    'kategori' => 'lab_psikotes',
                    'keterangan' => 'Hasil pemeriksaan elektrofisiologi pendengaran pra-terapi wicara dari RS Rujukan',
                ]
            );

            EmrAttachment::firstOrCreate(
                [
                    'profile_anak_id' => $child->id,
                    'nama_file' => "Laporan_Asesmen_Psikologis_WISC_{$child->nama_anak}.pdf",
                ],
                [
                    'file_path' => "/uploads/berkas/wisc-{$child->id}.pdf",
                    'file_type' => 'application/pdf',
                    'file_size' => 458000,
                    'kategori' => 'lab_psikotes',
                    'keterangan' => 'Laporan profil kognitif dan atensi dari Psikolog Anak Klinis',
                ]
            );
        }

        // ================= 5. SEED ROLE MODULE PERMISSIONS (V, C, U, D) =================
        $roles = Role::where('status_aktif', true)->get();
        $modules = [
            ['code' => 'pendaftaran', 'name' => 'Pendaftaran & Antrian Pasien'],
            ['code' => 'emr', 'name' => 'Rekam Medis Elektronik (RME / SOAP)'],
            ['code' => 'asesmen', 'name' => 'Asesmen & Evaluasi Terapi Anak'],
            ['code' => 'jadwal', 'name' => 'Penjadwalan & Mapping Praktik'],
            ['code' => 'kasir', 'name' => 'Kasir POS & Billing Penjamin'],
            ['code' => 'logistik', 'name' => 'Farmasi, Gudang & Logistik (PR/PO/GRN)'],
            ['code' => 'dokumen', 'name' => 'Dokumen Medis & Berkas Pasien'],
            ['code' => 'laporan', 'name' => 'Laporan Klinik & Keuangan'],
            ['code' => 'settings', 'name' => 'Pengaturan Sistem & Master Data'],
        ];

        foreach ($roles as $r) {
            $roleSlug = strtolower(str_replace([' ', '-'], '_', $r->role));
            $isSuper = in_array($roleSlug, ['super_admin', 'superadmin', 'admin', 'administrator']);
            $isDokter = str_contains($roleSlug, 'dokter');
            $isTerapis = str_contains($roleSlug, 'terapis');
            $isKasir = str_contains($roleSlug, 'kasir');
            $isLogistik = str_contains($roleSlug, 'logistik') || str_contains($roleSlug, 'farmasi') || str_contains($roleSlug, 'gudang') || str_contains($roleSlug, 'apoteker');
            $isPendaftaran = str_contains($roleSlug, 'pendaftaran') || str_contains($roleSlug, 'resepsionis');
            $isOrangTua = str_contains($roleSlug, 'orang_tua') || str_contains($roleSlug, 'orangtua') || str_contains($roleSlug, 'wali');

            foreach ($modules as $mod) {
                $v = false; $c = false; $u = false; $d = false;

                if ($isSuper) {
                    $v = true; $c = true; $u = true; $d = true;
                } elseif ($isDokter) {
                    if (in_array($mod['code'], ['emr', 'asesmen', 'jadwal', 'dokumen'])) {
                        $v = true; $c = true; $u = true;
                    } elseif ($mod['code'] === 'pendaftaran') {
                        $v = true;
                    }
                } elseif ($isTerapis) {
                    if (in_array($mod['code'], ['emr', 'asesmen', 'jadwal'])) {
                        $v = true; $c = true; $u = true;
                    } elseif ($mod['code'] === 'pendaftaran') {
                        $v = true;
                    }
                } elseif ($isKasir) {
                    if ($mod['code'] === 'kasir') {
                        $v = true; $c = true; $u = true;
                    } elseif (in_array($mod['code'], ['pendaftaran', 'laporan'])) {
                        $v = true;
                    }
                } elseif ($isLogistik) {
                    if ($mod['code'] === 'logistik') {
                        $v = true; $c = true; $u = true;
                    }
                } elseif ($isPendaftaran) {
                    if (in_array($mod['code'], ['pendaftaran', 'jadwal', 'dokumen'])) {
                        $v = true; $c = true; $u = true;
                    }
                } elseif ($isOrangTua) {
                    if (in_array($mod['code'], ['pendaftaran', 'emr', 'kasir'])) {
                        $v = true;
                    }
                }

                RoleModulePermission::updateOrCreate(
                    ['role_id' => $r->id, 'module_code' => $mod['code']],
                    [
                        'module_name' => $mod['name'],
                        'can_view' => $v,
                        'can_create' => $c,
                        'can_update' => $u,
                        'can_delete' => $d,
                    ]
                );
            }
        }
    }
}
