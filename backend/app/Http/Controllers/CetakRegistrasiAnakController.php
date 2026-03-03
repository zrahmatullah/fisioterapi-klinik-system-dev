<?php

namespace App\Http\Controllers;

use App\Models\RegistrasiAnak;
use Barryvdh\DomPDF\Facade\Pdf;

class CetakRegistrasiAnakController extends Controller
{
    public function cetak($id)
    {
        $data = RegistrasiAnak::with([
            'profileAnak',
            'terapis',
            'ruangan'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('registrasi-anak', [
            'data' => $data
        ])->setPaper('A4', 'portrait');

        // 🔹 STREAM → BUKA DI TAB BROWSER
        return $pdf->stream(
            'formulir-pendaftaran-' . $data->no_regis . '.pdf'
        );
    }

    public function cetakAntrian($id)
    {
        $data = RegistrasiAnak::with([
            'profileAnak',
            'terapis',
            'ruangan'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('antrian-anak', [
            'data' => $data
        ])
        ->setPaper([0, 0, 226.77, 600]); // 🔥 ukuran struk thermal 80mm

        return $pdf->stream(
            'antrian-' . $data->no_antrian . '.pdf'
        );
    }

}
