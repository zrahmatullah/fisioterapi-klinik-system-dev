<?php

namespace App\Http\Controllers\Api;

// use AntrianDipanggil;
use App\Events\AntrianDipanggil;
use App\Http\Controllers\Controller;
use App\Mail\PengingatJadwalTerapiMail;
use App\Models\JadwalUser;
use App\Models\RegistrasiAnak;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
class RegistrasiAnakController extends Controller
{
    public function index()
    {
        return RegistrasiAnak::with([
            'profileAnak',
            'profileAnak.jenisKelamin',
            'terapis',
            'ruangan',
            'pelayanans.layanan',
            'pelayanans.terapis',
            'pembayarans'
        ])
            ->orderBy('tgl_regis', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'profile_anak_id' => 'required|exists:profile_anak,id',
            'tgl_regis'       => 'required|date',
            'terapis_id'      => 'required|exists:user_profile,id',
            'ruangan_id'      => 'required|exists:ruangan,id',
            'nama_ayah'          => 'nullable|string|max:255',
            'nama_ibu'           => 'nullable|string|max:255',
            'notelp'             => 'nullable|string|max:20',
            'usia_saat_menikah'  => 'nullable|integer|min:10|max:100',
            'alamat'             => 'nullable|string',
            'keluhan_saat_ini'   => 'nullable|string',
            'kemampuan_saat_ini' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {

            // ================= ID PASIEN =================
            $lastPasien = RegistrasiAnak::where('profile_anak_id', $request->profile_anak_id)
                ->orderBy('id_pasien', 'desc')
                ->first();

            if ($lastPasien) {
                $number = (int) substr($lastPasien->id_pasien, strrpos($lastPasien->id_pasien, '-') + 1);
                $number++;
                $idPasien = 'PA-' . $request->profile_anak_id . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
            } else {
                $idPasien = 'PA-' . $request->profile_anak_id . '-001';
            }

            // ================= NO REGISTRASI =================
            // $today = now()->format('Ymd');
            // $lastReg = RegistrasiAnak::whereDate('created_at', now())->count() + 1;
            // $noRegis = 'REG-' . $today . '-' . str_pad($lastReg, 4, '0', STR_PAD_LEFT);
            // ================= NO REGISTRASI (AMAN) =================
            $today = now()->format('Ymd');

            $seq = DB::selectOne(
                "SELECT nextval('registrasi_anak_no_regis_seq') as seq"
            )->seq;

            $noRegis = 'REG-' . $today . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            // ================= NO ANTRIAN =================
            $tanggal = Carbon::parse($request->tgl_regis)->toDateString();

            // Hitung antrian hari ini (opsional per ruangan)
            $lastAntrian = RegistrasiAnak::whereDate('tgl_regis', $tanggal)
                ->where('ruangan_id', $request->ruangan_id) // kalau per ruangan
                ->count();

            $noAntrian = 'A-' . str_pad($lastAntrian + 1, 3, '0', STR_PAD_LEFT);



            // ================= CREATE =================
            $registrasi = RegistrasiAnak::create([
                'profile_anak_id'   => $request->profile_anak_id,
                'id_pasien'         => $idPasien,
                'no_regis'          => $noRegis,
                'no_antrian'        => $noAntrian,
                'tgl_regis'         => $request->tgl_regis,
                'terapis_id'        => $request->terapis_id,
                'ruangan_id'        => $request->ruangan_id,
                'status_kedatangan' => 'belum_datang',
                'status_pelayanan'  => 'belum_dilayani',

                // ===== DATA ORANG TUA & KONDISI =====
                'nama_ayah'          => $request->nama_ayah,
                'nama_ibu'           => $request->nama_ibu,
                'notelp'             => $request->notelp,
                'usia_saat_menikah'  => $request->usia_saat_menikah,
                'alamat'             => $request->alamat,
                'keluhan_saat_ini'   => $request->keluhan_saat_ini,
                'kemampuan_saat_ini' => $request->kemampuan_saat_ini,
            ]);

            return response()->json(
                $registrasi->load(['profileAnak', 'profileAnak.jenisKelamin', 'terapis', 'ruangan']),
                201
            );
        });
    }

    public function show($id)
    {
        return RegistrasiAnak::with([
            'profileAnak',
            'profileAnak.jenisKelamin',
            'terapis',
            'ruangan',
            'pelayanans.layanan',
            'pelayanans.terapis',
            'pembayarans'
        ])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $registrasi = RegistrasiAnak::findOrFail($id);

        $request->validate([
            'tgl_regis'          => 'sometimes|required|date',
            'terapis_id'         => 'sometimes|required|exists:user_profile,id',
            'ruangan_id'         => 'sometimes|required|exists:ruangan,id',
            'status_kedatangan'  => ['sometimes', Rule::in(['belum_datang', 'sudah_datang', 'tidak_hadir'])],
            'status_pelayanan'   => ['sometimes', Rule::in(['belum_dilayani', 'sedang_dilayani', 'selesai', 'batal'])],

            // ===== DATA ORANG TUA & KONDISI =====
            'nama_ayah'          => 'sometimes|nullable|string|max:255',
            'nama_ibu'           => 'sometimes|nullable|string|max:255',
            'notelp'             => 'sometimes|nullable|string|max:20',
            'usia_saat_menikah'  => 'sometimes|nullable|integer|min:10|max:100',
            'alamat'             => 'sometimes|nullable|string',
            'keluhan_saat_ini'   => 'sometimes|nullable|string',
            'kemampuan_saat_ini' => 'sometimes|nullable|string',
        ]);

        $registrasi->update($request->only([
            'tgl_regis',
            'terapis_id',
            'ruangan_id',
            'status_kedatangan',
            'status_pelayanan',

            'nama_ayah',
            'nama_ibu',
            'notelp',
            'usia_saat_menikah',
            'alamat',
            'keluhan_saat_ini',
            'kemampuan_saat_ini',
        ]));

        return response()->json(
            $registrasi->load(['profileAnak', 'profileAnak.jenisKelamin', 'terapis', 'ruangan'])
        );
    }

    public function destroy($id)
    {
        $registrasi = RegistrasiAnak::findOrFail($id);
        $registrasi->delete();

        return response()->json([
            'message' => 'Registrasi anak berhasil dihapus'
        ]);
    }

    public function terapisByTanggal(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date'
        ]);

        $hari = Carbon::parse($request->tanggal)->isoWeekday();

        $terapisIds = JadwalUser::whereHas('jadwalMaster', function ($q) use ($hari) {
            $q->where('hari', $hari)
            ->where('status_aktif', true);
        })
            ->where('status_aktif', true)
            ->pluck('user_profile_id')
            ->unique();

        // dd($hari, $terapisIds);

        $terapis = UserProfile::with('ruangans')
            ->whereIn('id', $terapisIds)
            ->where('jenis_user_id', 9)
            ->orderBy('nama')
            ->get();


        return response()->json([
            'success' => true,
            'data' => $terapis
        ]);
    }

    public function kirimEmailJadwal($id)
    {
        $registrasi = RegistrasiAnak::with([
            'profileAnak.orangTua',
            'profileAnak.jenisKelamin',
            'pelayanans.layanan',
            'pelayanans.terapis',
            'ruangan'
        ])->findOrFail($id);

        $orangTua = $registrasi->profileAnak->orangTua;

        if (!$orangTua || !$orangTua->email) {
            return response()->json([
                'message' => 'Email orang tua tidak ditemukan'
            ], 422);
        }

        Mail::to($orangTua->email)
            ->send(new PengingatJadwalTerapiMail($registrasi));

        return response()->json([
            'message' => 'Email pengingat berhasil dikirim'
        ]);
    }
    public function updateKedatangan(Request $request, $id)
    {
        $request->validate([
            'status_kedatangan' => ['required', Rule::in(['belum_datang', 'sudah_datang', 'tidak_hadir'])],
        ]);

        $registrasi = RegistrasiAnak::findOrFail($id);

        // Jika sudah datang, jangan timpa waktu lagi
        if (
            $registrasi->status_kedatangan === 'sudah_datang' &&
            $request->status_kedatangan === 'sudah_datang'
        ) {
            return response()->json([
                'message' => 'Sudah ditandai hadir',
                'data' => $registrasi
            ]);
        }

        $data = [
            'status_kedatangan' => $request->status_kedatangan,
        ];

        if ($request->status_kedatangan === 'sudah_datang') {
            $data['waktu_kedatangan'] = now();
        } else {
            $data['waktu_kedatangan'] = null;
        }

        $registrasi->update($data);

        return response()->json([
            'message' => 'Status kedatangan & waktu berhasil diperbarui',
            'data' => $registrasi->fresh()
        ]);
    }

    public function panggil($id)
    {
        $registrasi = RegistrasiAnak::with([
            'profileAnak',
            'terapis',
            'ruangan'
        ])->findOrFail($id);

        $registrasi->update([
            'status_pelayanan' => 'dipanggil',
            'waktu_panggil' => now()
        ]);

        $data = [
            'no_antrian' => $registrasi->no_antrian,
            'nama_anak' => $registrasi->profileAnak->nama_anak,
            'ruangan' => $registrasi->ruangan->ruangan,
            'terapis' => $registrasi->terapis->nama
        ];

        // event(new AntrianDipanggil($data)); 
        broadcast(new AntrianDipanggil($data));

        return response()->json([
            'message' => 'Antrian dipanggil',
            'data' => $data
        ]);
    }

}