<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kode Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f6f8; padding:20px;">
    <div style="max-width:600px; margin:auto; background:#ffffff; padding:30px; border-radius:8px;">
        <h2 style="color:#333;">Reset Password Akun Anda</h2>

        <p>Halo,</p>

        <p>
            Kami menerima permintaan untuk mereset password akun Anda.
            Silakan masukkan kode OTP berikut untuk melanjutkan:
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
            Kode ini hanya berlaku selama <strong>5 menit</strong>.
            Jangan bagikan kode ini kepada siapa pun.
        </p>

        <p style="color:#64748b;">
            Jika Anda tidak merasa meminta reset password, abaikan email ini.
        </p>

        <hr>

        <p style="font-size:12px; color:#94a3b8;">
            Email ini dikirim otomatis oleh sistem.<br>
            © {{ date('Y') }} Sistem Informasi Klinik Abqary
        </p>
    </div>
</body>
</html>
