<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PelayananTerapiAnak;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelayananTerapiAnakController extends Controller
{
    public function index($registrasiId)
    {
        return PelayananTerapiAnak::with([
            'layanan',
            'terapis:id,nama'
        ])
            ->where('registrasi_anak_id', $registrasiId)
            ->orderBy('tanggal_penjadwalan')
            ->orderBy('jam_mulai')
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'registrasi_anak_id'  => 'required|exists:registrasi_anak,id',
            'layanan_id'          => 'required|exists:layanan,id',
            'tanggal_penjadwalan' => 'required|array',
        ]);

        return DB::transaction(function () use ($request) {

            $layanan = Layanan::findOrFail($request->layanan_id);

            if (count($request->tanggal_penjadwalan) !== (int) $layanan->qty) {
                abort(422, 'Jumlah tanggal tidak sesuai dengan qty layanan');
            }

            $rows = [];

            foreach ($request->tanggal_penjadwalan as $item) {

                if (is_array($item)) {
                    $tanggal    = $item['tanggal'] ?? null;
                    $jamMulai   = $item['jam_mulai'] ?? null;
                    $jamSelesai = $item['jam_selesai'] ?? null;
                } else {
                    $tanggal    = $item;
                    $jamMulai   = null;
                    $jamSelesai = null;
                }

                if (!$tanggal) {
                    abort(422, 'Format tanggal penjadwalan tidak valid');
                }

                $rows[] = PelayananTerapiAnak::create([
                    'registrasi_anak_id'  => $request->registrasi_anak_id,
                    'layanan_id'          => $layanan->id,
                    'terapis_id'          => null,
                    'qty'                 => 1,
                    'harga'               => $layanan->harga,
                    'tanggal_penjadwalan' => $tanggal,
                    'jam_mulai'           => $jamMulai,
                    'jam_selesai'         => $jamSelesai,
                    'status'              => 'terjadwal',
                ]);
            }

            return response()->json([
                'message'    => 'Layanan terapi berhasil ditambahkan',
                'total_sesi' => count($rows),
                'data'       => $rows
            ], 201);
        });
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:terjadwal,hadir,tidak_hadir,selesai,batal,pending_reschedule'
        ]);

        $data = PelayananTerapiAnak::findOrFail($id);
        $data->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status berhasil diperbarui',
            'data'    => $data
        ]);
    }

    public function updateTerapis(Request $request, $id)
    {
        $request->validate([
            'terapis_id' => 'required|exists:user_profile,id'
        ]);

        $data = PelayananTerapiAnak::findOrFail($id);
        $data->update(['terapis_id' => $request->terapis_id]);

        return response()->json([
            'message' => 'Terapis berhasil ditentukan',
            'data'    => $data->load('terapis')
        ]);
    }

    public function destroy($id)
    {
        PelayananTerapiAnak::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Data layanan terapi berhasil dihapus'
        ]);
    }

    /* ======================================================
       =============== RESCHEDULE FEATURE ===================
       ====================================================== */

    // ORANG TUA AJUKAN RESCHEDULE
    public function ajukanReschedule(Request $request, $id)
    {
        $request->validate([
            'tanggal_baru' => 'required|date'
        ]);

        $data = PelayananTerapiAnak::findOrFail($id);

        // Hanya boleh reschedule jika masih terjadwal
        if ($data->status !== 'terjadwal') {
            return response()->json([
                'message' => 'Hanya jadwal dengan status terjadwal yang bisa di-reschedule'
            ], 422);
        }

        $data->update([
            'tanggal_reschedule_request' => $request->tanggal_baru,
            'status' => 'pending_reschedule'
        ]);

        return response()->json([
            'message' => 'Permintaan reschedule berhasil dikirim, menunggu konfirmasi admin',
            'data'    => $data
        ]);
    }

    // ADMIN SETUJUI RESCHEDULE
    public function approveReschedule($id)
    {
        $data = PelayananTerapiAnak::findOrFail($id);

        if (!$data->tanggal_reschedule_request) {
            return response()->json([
                'message' => 'Tidak ada permintaan reschedule'
            ], 422);
        }

        $data->update([
            'tanggal_penjadwalan' => $data->tanggal_reschedule_request,
            'tanggal_reschedule_request' => null,
            'status' => 'terjadwal'
        ]);

        return response()->json([
            'message' => 'Reschedule berhasil disetujui',
            'data'    => $data
        ]);
    }

    // ADMIN TOLAK RESCHEDULE
    public function rejectReschedule($id)
    {
        $data = PelayananTerapiAnak::findOrFail($id);

        $data->update([
            'tanggal_reschedule_request' => null,
            'status' => 'terjadwal'
        ]);

        return response()->json([
            'message' => 'Reschedule ditolak',
            'data'    => $data
        ]);
    }
    public function listPendingReschedule()
    {
        return PelayananTerapiAnak::with([
            'registrasi.profileAnak',
            'layanan',
            'terapis'
        ])
            ->where('status', 'pending_reschedule')
            ->orderBy('tanggal_reschedule_request')
            ->get();
    }

}
