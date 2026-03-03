<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kode OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f6f8; padding:20px;">
    <div style="max-width:600px; margin:auto; background:#ffffff; padding:30px; border-radius:8px;">
        <h2 style="color:#333;">Verifikasi Email Anda</h2>

        <p>Halo,</p>

        <p>
            Terima kasih telah menggunakan layanan Sistem Informasi Terapi Anak.
            Untuk melanjutkan proses verifikasi akun Anda, silakan masukkan kode OTP berikut:
        </p>

        <div style="
            font-size:32px;
            letter-spacing:8px;
            font-weight:bold;
            text-align:center;
            background:#f1f5f9;
            padding:15px;
            border-radius:6px;
            margin:20px 0;
            color:#0f172a;
        ">
            {{ $otp }}
        </div>

        <p>
            Kode OTP ini bersifat rahasia dan hanya berlaku selama <strong>5 menit</strong>.
            Demi keamanan akun Anda, jangan membagikan kode ini kepada siapa pun.
        </p>

        <p style="color:#64748b;">
            Apabila Anda tidak merasa melakukan permintaan verifikasi ini, silakan abaikan email ini.
            Tidak ada tindakan lebih lanjut yang perlu Anda lakukan.
        </p>

        <hr>

        <p style="font-size:12px; color:#94a3b8;">
            Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.<br>
            © {{ date('Y') }}Sistem Informasi Klinik Abqary. Semua hak dilindungi.
        </p>
    </div>
</body>
</html>
