<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistrasiAnak;
use Illuminate\Support\Facades\Mail;
use PDF;

class InvoiceController extends Controller
{
    /**
     * Cetak / Preview Invoice
     */
    public function print($id)
    {
        $registrasi = RegistrasiAnak::with([
            'profileAnak',
            'ruangan',
            'terapis',
            'pelayanans.layanan',
            'pelayanans.terapis',
            'pembayarans'
        ])->findOrFail($id);

        $pembayaran = $registrasi->pembayarans->last();

        $pdf = PDF::loadView('invoice', compact('registrasi', 'pembayaran'));

        $pdf->setPaper([0, 0, 220 * 2.83465, 1000]);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-bottom', 0);

        return $pdf->stream("Invoice-{$registrasi->no_regis}.pdf");
    }

    /**
     * Kirim Invoice ke Email User
     */
    public function sendEmail($id)
    {
        $registrasi = RegistrasiAnak::with([
            'profileAnak.userProfile', // ⬅️ WAJIB
            'ruangan',
            'terapis',
            'pelayanans.layanan',
            'pelayanans.terapis',
            'pembayarans'
        ])->findOrFail($id);

        // dd(
        //     $registrasi->profileAnak,
        //     optional($registrasi->profileAnak)->userProfile
        // );

        // ✅ AMBIL EMAIL DARI user_profile
        $email = $registrasi->profileAnak?->userProfile?->email;

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Email orang tua belum diisi'
            ], 422);
        }

        $pembayaran = $registrasi->pembayarans->last();

        $pdf = PDF::loadView('invoice', compact('registrasi', 'pembayaran'));

        Mail::send('emails.invoice-email', [
            'registrasi' => $registrasi,
            'pembayaran' => $pembayaran
        ], function ($message) use ($email, $registrasi, $pdf) {
            $message->to($email)
                ->subject('Invoice Pembayaran - ' . $registrasi->no_regis)
                ->attachData(
                    $pdf->output(),
                    "Invoice-{$registrasi->no_regis}.pdf",
                    ['mime' => 'application/pdf']
                );
        });


        return response()->json([
            'success' => true,
            'message' => 'Invoice berhasil dikirim ke email'
        ]);
    }
}
