<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PembayaranTransaksi;
use App\Models\ShiftKasir;
use App\Models\PaketTerapiPasien;
use App\Models\ProfileAnak;
use App\Models\RegistrasiAnak;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasirBillingController extends Controller
{
    // ================= 1. LIST INVOICES =================
    public function indexInvoices(Request $request)
    {
        $query = Invoice::with(['profileAnak', 'items', 'payments.kasir'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('profile_anak_id')) {
            $query->where('profile_anak_id', $request->profile_anak_id);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->tanggal_mulai)->startOfDay(),
                Carbon::parse($request->tanggal_selesai)->endOfDay(),
            ]);
        }

        $invoices = $query->paginate($request->query('per_page', 15));
        return response()->json($invoices);
    }

    // ================= 2. STORE INVOICE =================
    public function storeInvoice(Request $request)
    {
        $request->validate([
            'profile_anak_id' => 'required|exists:profile_anak,id',
            'registrasi_anak_id' => 'nullable|exists:registrasi_anak,id',
            'jenis_penjamin' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.tipe_item' => 'required|string',
            'items.*.deskripsi' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $today = now()->format('Ymd');
            $countToday = Invoice::whereDate('created_at', now())->count() + 1;
            $noInvoice = "INV-{$today}-" . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += ($item['qty'] * $item['harga_satuan']);
            }

            $diskon = $request->diskon ?? 0;
            $totalTagihan = max(0, $subtotal - $diskon);

            $invoice = Invoice::create([
                'nomor_invoice' => $noInvoice,
                'profile_anak_id' => $request->profile_anak_id,
                'registrasi_anak_id' => $request->registrasi_anak_id,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'pajak' => 0,
                'total_tagihan' => $totalTagihan,
                'total_terbayar' => 0,
                'sisa_tagihan' => $totalTagihan,
                'status' => 'belum_bayar',
                'jenis_penjamin' => $request->jenis_penjamin ?? 'Umum',
                'catatan' => $request->catatan,
                'created_by' => auth('api')->id(),
            ]);

            foreach ($request->items as $item) {
                $itemSubtotal = ($item['qty'] * $item['harga_satuan']);
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'tipe_item' => $item['tipe_item'],
                    'item_id' => $item['item_id'] ?? null,
                    'deskripsi' => $item['deskripsi'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'diskon' => 0,
                    'subtotal' => $itemSubtotal,
                ]);

                // Jika item adalah paket terapi, buat otomatis paket terapi pasien
                if ($item['tipe_item'] === 'paket_terapi' && isset($item['total_sesi'])) {
                    $noPaket = "PKT-{$today}-" . rand(1000, 9999);
                    PaketTerapiPasien::create([
                        'profile_anak_id' => $request->profile_anak_id,
                        'layanan_id' => $item['item_id'] ?? 3,
                        'invoice_id' => $invoice->id,
                        'nomor_paket' => $noPaket,
                        'nama_paket' => $item['deskripsi'],
                        'total_sesi' => $item['total_sesi'],
                        'sesi_terpakai' => 0,
                        'sisa_sesi' => $item['total_sesi'],
                        'tanggal_mulai' => now()->toDateString(),
                        'tanggal_berakhir' => now()->addMonths(6)->toDateString(),
                        'status' => 'aktif',
                    ]);
                }
            }

            return response()->json([
                'message' => 'Tagihan berhasil dibuat',
                'invoice' => $invoice->load('items', 'profileAnak'),
            ], 201);
        });
    }

    // ================= 3. PROSES PEMBAYARAN =================
    public function bayar(Request $request, $id)
    {
        $request->validate([
            'metode_bayar' => 'required|in:tunai,transfer,qris,kartu_debit,kartu_kredit',
            'jumlah_bayar' => 'required|numeric|min:1',
            'nomor_referensi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $invoice = Invoice::findOrFail($id);
            $user = auth('api')->user();

            $today = now()->format('Ymd');
            $noTrx = "TRX-{$today}-" . rand(10000, 99999);

            $kembalian = max(0, $request->jumlah_bayar - $invoice->sisa_tagihan);
            $pembayaranAktif = min($request->jumlah_bayar, $invoice->sisa_tagihan);

            // Cek apakah ada shift kasir terbuka
            $shift = ShiftKasir::where('kasir_id', $user?->user_profile_id ?? 1)
                ->where('status', 'open')
                ->latest()
                ->first();

            $transaksi = PembayaranTransaksi::create([
                'invoice_id' => $invoice->id,
                'nomor_transaksi' => $noTrx,
                'kasir_id' => $user?->user_profile_id,
                'shift_kasir_id' => $shift?->id,
                'metode_bayar' => $request->metode_bayar,
                'jumlah_bayar' => $request->jumlah_bayar,
                'kembalian' => $kembalian,
                'nomor_referensi' => $request->nomor_referensi,
                'status' => 'sukses',
                'catatan' => $request->catatan,
            ]);

            // Update shift kasir total
            if ($shift) {
                if ($request->metode_bayar === 'tunai') {
                    $shift->increment('total_tunai_sistem', $pembayaranAktif);
                } else {
                    $shift->increment('total_non_tunai_sistem', $pembayaranAktif);
                }
            }

            // Update status invoice
            $invoice->total_terbayar += $pembayaranAktif;
            $invoice->sisa_tagihan = max(0, $invoice->total_tagihan - $invoice->total_terbayar);

            if ($invoice->sisa_tagihan <= 0) {
                $invoice->status = 'lunas';
            } else {
                $invoice->status = 'bayar_sebagian';
            }
            $invoice->save();

            return response()->json([
                'message' => 'Pembayaran berhasil diproses',
                'transaksi' => $transaksi,
                'invoice' => $invoice->fresh(['items', 'profileAnak', 'payments']),
            ]);
        });
    }

    // ================= 4. SHIFT KASIR =================
    public function shiftStatus()
    {
        $user = auth('api')->user();
        $kasirId = $user?->user_profile_id ?? 1;

        $shift = ShiftKasir::where('kasir_id', $kasirId)
            ->where('status', 'open')
            ->latest()
            ->first();

        return response()->json([
            'is_open' => !is_null($shift),
            'shift' => $shift,
        ]);
    }

    public function bukaShift(Request $request)
    {
        $request->validate(['saldo_awal' => 'required|numeric|min:0']);

        $user = auth('api')->user();
        $kasirId = $user?->user_profile_id ?? 1;

        // Tutup shift lama jika lupa
        ShiftKasir::where('kasir_id', $kasirId)->where('status', 'open')->update([
            'status' => 'closed',
            'waktu_tutup' => now(),
        ]);

        $shift = ShiftKasir::create([
            'kasir_id' => $kasirId,
            'waktu_buka' => now(),
            'saldo_awal' => $request->saldo_awal,
            'total_tunai_sistem' => 0,
            'total_non_tunai_sistem' => 0,
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Shift kasir berhasil dibuka',
            'shift' => $shift,
        ], 201);
    }

    public function tutupShift(Request $request)
    {
        $request->validate([
            'saldo_akhir_fisik' => 'required|numeric|min:0',
            'catatan_shift' => 'nullable|string',
        ]);

        $user = auth('api')->user();
        $kasirId = $user?->user_profile_id ?? 1;

        $shift = ShiftKasir::where('kasir_id', $kasirId)
            ->where('status', 'open')
            ->latest()
            ->firstOrFail();

        $totalEkspektasi = $shift->saldo_awal + $shift->total_tunai_sistem;
        $selisih = $request->saldo_akhir_fisik - $totalEkspektasi;

        $shift->update([
            'waktu_tutup' => now(),
            'saldo_akhir_fisik' => $request->saldo_akhir_fisik,
            'selisih' => $selisih,
            'status' => 'closed',
            'catatan_shift' => $request->catatan_shift,
        ]);

        return response()->json([
            'message' => 'Shift kasir berhasil ditutup dengan rekonsiliasi',
            'shift' => $shift,
        ]);
    }

    // ================= 5. PAKET TERAPI PASIEN =================
    public function paketPasien($profileAnakId)
    {
        $pakets = PaketTerapiPasien::where('profile_anak_id', $profileAnakId)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($pakets);
    }

    // ================= 6. DATA THERMAL RECEIPT (58mm / 80mm) =================
    public function thermalReceipt($invoiceId)
    {
        $invoice = Invoice::with(['profileAnak', 'items', 'payments.kasir'])->findOrFail($invoiceId);

        return response()->json([
            'klinik' => [
                'nama' => 'KLINIK TERAPI TUMBUH KEMBANG',
                'alamat' => 'Jl. Kesehatan Anak No. 123, Jakarta Selatan',
                'telepon' => '021-7890123',
            ],
            'invoice' => [
                'nomor' => $invoice->nomor_invoice,
                'tanggal' => $invoice->created_at->format('d/m/Y H:i'),
                'pasien' => $invoice->profileAnak?->nama_anak,
                'no_rm' => $invoice->profileAnak?->no_rm,
                'items' => $invoice->items->map(function ($it) {
                    return [
                        'nama' => $it->deskripsi,
                        'qty' => $it->qty,
                        'harga' => number_format($it->harga_satuan, 0, ',', '.'),
                        'subtotal' => number_format($it->subtotal, 0, ',', '.'),
                    ];
                }),
                'subtotal' => number_format($invoice->subtotal, 0, ',', '.'),
                'diskon' => number_format($invoice->diskon, 0, ',', '.'),
                'total' => number_format($invoice->total_tagihan, 0, ',', '.'),
                'terbayar' => number_format($invoice->total_terbayar, 0, ',', '.'),
                'kembali' => number_format($invoice->payments->last()?->kembalian ?? 0, 0, ',', '.'),
                'metode' => strtoupper($invoice->payments->last()?->metode_bayar ?? 'TUNAI'),
                'status' => strtoupper($invoice->status),
            ]
        ]);
    }
}
