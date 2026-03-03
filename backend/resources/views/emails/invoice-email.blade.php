<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice Pembayaran</title>
</head>
<body style="
    margin:0;
    padding:0;
    background:#f1f5f9;
    font-family: Arial, Helvetica, sans-serif;
">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 0;">
        <tr>
            <td align="center">

                <!-- MAIN CARD -->
                <table width="640" cellpadding="0" cellspacing="0" style="
                    background:#ffffff;
                    border-radius:20px;
                    overflow:hidden;
                    box-shadow:0 12px 30px rgba(0,0,0,0.08);
                ">

                    <!-- HEADER -->
                    <tr>
                        <td style="
                            background:#0ea5e9;
                            padding:28px 24px;
                            color:#ffffff;
                        ">
                            <h1 style="
                                margin:0;
                                font-size:22px;
                                font-weight:800;
                                letter-spacing:0.4px;
                            ">
                                Klinik Terapi Anak Abqary
                            </h1>
                            <p style="
                                margin:6px 0 0;
                                font-size:14px;
                                opacity:0.95;
                            ">
                                Invoice Resmi Pembayaran Layanan
                            </p>
                        </td>
                    </tr>

                    <!-- CONTENT -->
                    <tr>
                        <td style="padding:28px 24px; color:#0f172a;">

                            <!-- META INFO (LEFT - RIGHT) -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px; font-size:14px;">
                                <tr>
                                    <td align="left" style="vertical-align:top;">
                                        <p style="margin:0; color:#64748b;">No Registrasi</p>
                                        <p style="margin:6px 0 0; font-weight:700;">
                                            {{ $registrasi->no_regis }}
                                        </p>
                                    </td>
                                    <td align="right" style="vertical-align:top;">
                                        <p style="margin:0; color:#64748b;">Tanggal Penerbitan</p>
                                        <p style="margin:6px 0 0; font-weight:700;">
                                            {{ now()->format('d M Y') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- PATIENT CARD -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="
                                background:#f8fafc;
                                border:1px solid #e2e8f0;
                                border-radius:14px;
                                margin-bottom:26px;
                            ">
                                <tr>
                                    <td style="padding:18px;">
                                        <p style="
                                            margin:0 0 6px;
                                            font-size:13px;
                                            color:#0284c7;
                                            font-weight:700;
                                        ">
                                            Nama Pasien Anak
                                        </p>
                                        <p style="
                                            margin:0;
                                            font-size:17px;
                                            font-weight:800;
                                        ">
                                            {{ $registrasi->profileAnak->nama_anak }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- DETAIL TABLE -->
                            <table width="100%" cellpadding="12" cellspacing="0" style="
                                border-collapse:collapse;
                                font-size:14px;
                                margin-bottom:24px;
                            ">
                                <thead>
                                    <tr style="background:#f1f5f9;">
                                        <th align="left" style="border:1px solid #e2e8f0;">Layanan</th>
                                        <th align="center" style="border:1px solid #e2e8f0;">Jumlah sesi</th>
                                        <th align="right" style="border:1px solid #e2e8f0;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registrasi->pelayanans as $p)
                                        <tr>
                                            <td style="border:1px solid #e2e8f0;">
                                                {{ $p->layanan->layanan }}
                                            </td>
                                            <td align="center" style="border:1px solid #e2e8f0;">
                                                {{ $p->qty }}
                                            </td>
                                            <td align="right" style="border:1px solid #e2e8f0;">
                                                Rp {{ number_format($p->qty * $p->harga, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- TOTAL -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="
                                background:#ecfeff;
                                border:1px solid #bae6fd;
                                border-radius:16px;
                                margin-bottom:26px;
                            ">
                                <tr>
                                    <td align="right" style="padding:20px;">
                                        <p style="margin:0; font-size:14px; color:#0369a1;">
                                            Total Tagihan
                                        </p>
                                        <p style="
                                            margin:8px 0 0;
                                            font-size:22px;
                                            font-weight:900;
                                            color:#0c4a6e;
                                        ">
                                            Rp {{ number_format(
                                                $registrasi->pelayanans->sum(fn($p) => $p->qty * $p->harga),
                                                0, ',', '.'
                                            ) }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- NOTE -->
                            <p style="margin:0; font-size:13px; color:#334155; line-height:1.7;">
                            Invoice ini merupakan dokumen resmi yang diterbitkan oleh Klinik, terlampir dalam bentuk <strong>PDF</strong>.
                            </p>
                            <p style="margin:8px 0 0; font-size:13px; color:#334155;">
                            Apabila terdapat pertanyaan terkait rincian pembayaran atau layanan, silakan menghubungi pihak administrasi klinik pada jam operasional.
                            </p>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="
                            background:#f8fafc;
                            padding:20px;
                            text-align:center;
                            font-size:12px;
                            color:#64748b;
                        ">
                            Terima kasih atas kepercayaan Ayah & Bunda telah mempercayakan tumbuh kembang buah hati kepada kami  💙<br>
                            <strong style="color:#0f172a;"> Terapi Anak Abaqry </strong>
                        </td>
                    </tr>

                </table>
                <!-- END MAIN CARD -->

            </td>
        </tr>
    </table>

</body>
</html>
