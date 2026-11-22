<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratTemplate;
use App\Models\SuratKeluar;
use App\Models\ProfileAnak;
use App\Models\InformedConsent;
use App\Models\EmrAttachment;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SuratDokumenController extends Controller
{
    // ================= 1. TEMPLATES =================
    public function indexTemplates()
    {
        $templates = SuratTemplate::where('status_aktif', true)->get();
        return response()->json($templates);
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'kode_template' => 'required|string|unique:surat_templates,kode_template',
            'judul' => 'required|string',
            'jenis_surat' => 'required|string',
            'template_content' => 'required|string',
        ]);

        $template = SuratTemplate::create($request->all());
        return response()->json(['message' => 'Template berhasil dibuat', 'template' => $template], 201);
    }

    // ================= 2. GENERATE SURAT DENGAN PLACEHOLDER =================
    public function generateSurat(Request $request)
    {
        $request->validate([
            'surat_template_id' => 'required|exists:surat_templates,id',
            'profile_anak_id' => 'required|exists:profile_anak,id',
            'parameter_data' => 'required|array',
            'tanggal_surat' => 'nullable|date',
        ]);

        $template = SuratTemplate::findOrFail($request->surat_template_id);
        $pasien = ProfileAnak::with(['orangTua', 'walis'])->findOrFail($request->profile_anak_id);

        $params = $request->parameter_data;
        $params['nama_pasien'] = $pasien->nama_anak;
        $params['no_rm'] = $pasien->no_rm ?? '-';
        $params['tanggal_lahir'] = $pasien->tanggal_lahir ? Carbon::parse($pasien->tanggal_lahir)->translatedFormat('d F Y') : '-';
        $params['usia'] = $pasien->usia_lengkap;
        $params['nama_orang_tua'] = $params['nama_orang_tua'] ?? $pasien->orangTua?->nama ?? '-';

        // Ganti placeholder {{key}}
        $content = $template->template_content;
        foreach ($params as $key => $val) {
            $content = str_replace('{{' . $key . '}}', htmlspecialchars($val ?? ''), $content);
        }

        // Format Nomor Surat Otomatis
        $year = date('Y');
        $month = date('m');
        $romanMonths = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $romanMonth = $romanMonths[(int)$month];
        $count = SuratKeluar::whereYear('created_at', $year)->count() + 1;
        $seq = str_pad($count, 3, '0', STR_PAD_LEFT);

        $nomorSurat = str_replace(
            ['{SEQ}', '{YEAR}', '{MONTH}', '{ROMAN_MONTH}'],
            [$seq, $year, $month, $romanMonth],
            $template->format_nomor
        );

        $qrCode = Str::random(32);

        $surat = SuratKeluar::create([
            'nomor_surat' => $nomorSurat,
            'surat_template_id' => $template->id,
            'profile_anak_id' => $pasien->id,
            'penandatangan_id' => auth('api')->user()?->user_profile_id,
            'judul_surat' => $template->judul,
            'parameter_data' => $params,
            'isi_final_html' => $content,
            'qr_verification_code' => $qrCode,
            'status' => 'final',
            'tanggal_surat' => $request->tanggal_surat ?? now()->toDateString(),
            'created_by' => auth('api')->id(),
        ]);

        return response()->json([
            'message' => 'Surat berhasil diterbitkan',
            'surat' => $surat->load('profileAnak', 'template'),
        ], 201);
    }

    // ================= 3. ARSIP SURAT PASIEN =================
    public function arsipSuratPasien($profileAnakId)
    {
        $suratList = SuratKeluar::with(['template', 'penandatangan'])
            ->where('profile_anak_id', $profileAnakId)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($suratList);
    }

    // ================= 4. PUBLIC QR VERIFIKASI KEASLIAN =================
    public function verifyQr($code)
    {
        $surat = SuratKeluar::with(['profileAnak', 'penandatangan'])
            ->where('qr_verification_code', $code)
            ->first();

        if (!$surat) {
            return response()->json([
                'status' => 'INVALID',
                'message' => 'Dokumen tidak ditemukan atau kode verifikasi tidak valid.',
            ], 404);
        }

        return response()->json([
            'status' => 'VALID',
            'dokumen' => [
                'nomor_surat' => $surat->nomor_surat,
                'judul' => $surat->judul_surat,
                'nama_pasien' => $surat->profileAnak?->nama_anak,
                'no_rm' => $surat->profileAnak?->no_rm,
                'tanggal_terbit' => $surat->tanggal_surat,
                'penandatangan' => $surat->penandatangan?->nama ?? 'Tenaga Medis Klinik',
                'status_keaslian' => 'TERVERIFIKASI ASLI',
            ]
        ]);
    }

    // ================= 5. BERKAS-BERKAS PASIEN (UNIFIED PORTFOLIO) =================

    public function getBerkasPasien($profileAnakId)
    {
        $pasien = ProfileAnak::with(['orangTua', 'walis'])->findOrFail($profileAnakId);

        // 1. Informed Consent (Persetujuan Digital dengan Signature)
        $consents = InformedConsent::where('profile_anak_id', $profileAnakId)
            ->orderBy('signed_at', 'desc')
            ->get();

        // 2. Surat Keluar & Keterangan Dokter Resmi (QR Verified)
        $suratResmi = SuratKeluar::with(['template', 'penandatangan'])
            ->where('profile_anak_id', $profileAnakId)
            ->orderBy('tanggal_surat', 'desc')
            ->get();

        // 3. Lampiran Berkas Medis / Lab / Psikotes / Rujukan Luar
        $attachments = EmrAttachment::where('profile_anak_id', $profileAnakId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'pasien' => $pasien,
            'summary' => [
                'total_informed_consent' => $consents->count(),
                'total_surat_resmi' => $suratResmi->count(),
                'total_lampiran_klinis' => $attachments->count(),
            ],
            'informed_consents' => $consents,
            'surat_resmi' => $suratResmi,
            'lampiran_klinis' => $attachments,
        ]);
    }

    public function uploadBerkasPasien(Request $request)
    {
        $request->validate([
            'profile_anak_id' => 'required|exists:profile_anak,id',
            'nama_file' => 'required|string|max:255',
            'kategori' => 'required|string|in:lab_psikotes,foto_sesi,video_sesi,rujukan_eksternal,berkas_lainnya',
            'keterangan' => 'nullable|string',
            'file_path' => 'nullable|string',
            'file_type' => 'nullable|string',
            'file_size' => 'nullable|integer',
        ]);

        $attachment = EmrAttachment::create([
            'emr_record_id' => $request->emr_record_id,
            'profile_anak_id' => $request->profile_anak_id,
            'nama_file' => $request->nama_file,
            'file_path' => $request->file_path ?? '/uploads/berkas/' . Str::slug($request->nama_file) . '-' . time() . '.pdf',
            'file_type' => $request->file_type ?? 'application/pdf',
            'file_size' => $request->file_size ?? 102400,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json([
            'message' => 'Berkas pasien berhasil diunggah dan diarsipkan ke rekam medis.',
            'attachment' => $attachment,
        ], 201);
    }

    public function storeInformedConsent(Request $request)
    {
        $request->validate([
            'profile_anak_id' => 'required|exists:profile_anak,id',
            'judul_persetujuan' => 'required|string',
            'tipe_persetujuan' => 'required|string',
            'pernyataan' => 'required|string',
            'nama_penandatangan' => 'required|string',
            'hubungan_wali' => 'required|string',
            'tanda_tangan_base64' => 'required|string',
        ]);

        $consent = InformedConsent::create([
            'profile_anak_id' => $request->profile_anak_id,
            'registrasi_anak_id' => $request->registrasi_anak_id,
            'judul_persetujuan' => $request->judul_persetujuan,
            'tipe_persetujuan' => $request->tipe_persetujuan,
            'pernyataan' => $request->pernyataan,
            'nama_penandatangan' => $request->nama_penandatangan,
            'hubungan_wali' => $request->hubungan_wali,
            'tanda_tangan_base64' => $request->tanda_tangan_base64,
            'ip_address' => $request->ip() ?? '127.0.0.1',
            'signed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Informed Consent bertanda tangan digital berhasil disimpan.',
            'consent' => $consent,
        ], 201);
    }

    public function deleteBerkasPasien($id)
    {
        $attachment = EmrAttachment::findOrFail($id);
        $attachment->delete();

        return response()->json([
            'message' => 'Berkas pasien berhasil dihapus dari arsip.',
        ]);
    }
}
