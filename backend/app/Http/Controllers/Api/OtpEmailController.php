<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpEmail;
use App\Models\OtpEmailLog;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OtpEmailController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $otp   = random_int(100000, 999999);

        $otpData = OtpEmail::create([
            'email'      => $email,
            'kode_otp'   => Hash::make($otp),
            'expired_at' => Carbon::now()->addMinutes(5),
            'is_used'    => false,
            'attempt'    => 0
        ]);

        try {
            Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Kode OTP Verifikasi Email');
            });

            OtpEmailLog::create([
                'otp_email_id' => $otpData->id,
                'email'        => $email,
                'status'       => 'success',
                'response'     => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP berhasil dikirim ke email'
            ]);
        } catch (\Exception $e) {

            OtpEmailLog::create([
                'otp_email_id' => $otpData->id,
                'email'        => $email,
                'status'       => 'failed',
                'response'     => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP'
            ], 500);
        }
    }


    public function verify(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'kode_otp' => 'required'
        ]);

        $otpData = OtpEmail::where('email', $request->email)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$otpData) {
            return response()->json([
                'success' => false,
                'message' => 'OTP tidak ditemukan'
            ], 404);
        }


        if (Carbon::now()->gt($otpData->expired_at)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP sudah kedaluwarsa'
            ], 422);
        }


        if ($otpData->attempt >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Percobaan OTP melebihi batas'
            ], 429);
        }


        if (!Hash::check($request->kode_otp, $otpData->kode_otp)) {
            $otpData->increment('attempt');

            return response()->json([
                'success' => false,
                'message' => 'Kode OTP salah'
            ], 422);
        }


        $otpData->update([
            'is_used' => true
        ]);

        UserProfile::where('email', $request->email)
            ->update([
                'email_verified_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP berhasil diverifikasi'
        ]);
    }

    public function sendReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $otp   = random_int(100000, 999999);

        $otpData = OtpEmail::create([
            'email'      => $email,
            'kode_otp'   => Hash::make($otp),
            'expired_at' => Carbon::now()->addMinutes(5),
            'is_used'    => false,
            'attempt'    => 0,
            'purpose'    => 'reset_password'
        ]);

        try {
            Mail::send('emails.otp-reset', ['otp' => $otp], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Kode Reset Password');
            });

            OtpEmailLog::create([
                'otp_email_id' => $otpData->id,
                'email'        => $email,
                'status'       => 'success',
                'response'     => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kode reset berhasil dikirim ke email'
            ]);
        } catch (\Exception $e) {

            OtpEmailLog::create([
                'otp_email_id' => $otpData->id,
                'email'        => $email,
                'status'       => 'failed',
                'response'     => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim kode reset'
            ], 500);
        }
    }

    public function verifyReset(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'kode_otp' => 'required'
        ]);

        $otpData = OtpEmail::where('email', $request->email)
            ->where('purpose', 'reset_password')
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$otpData) {
            return response()->json([
                'success' => false,
                'message' => 'Kode reset tidak ditemukan'
            ], 404);
        }

        if (Carbon::now()->gt($otpData->expired_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode reset sudah kedaluwarsa'
            ], 422);
        }

        if ($otpData->attempt >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Percobaan melebihi batas'
            ], 429);
        }

        if (!Hash::check($request->kode_otp, $otpData->kode_otp)) {
            $otpData->increment('attempt');

            return response()->json([
                'success' => false,
                'message' => 'Kode reset salah'
            ], 422);
        }

        // generate reset token
        $resetToken = bin2hex(random_bytes(32));

        $otpData->update([
            'is_used'     => true,
            'reset_token'=> Hash::make($resetToken)
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Kode valid',
            'reset_token'=> $resetToken
        ]);
    }


}