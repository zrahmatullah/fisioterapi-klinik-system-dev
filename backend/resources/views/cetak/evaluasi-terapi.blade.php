<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Laporan Evaluasi Terapi</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 11px;
        }

        .logo {
            position: absolute;
            left: 25;
            top:0;
            width: 99px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .report-title {
             text-align: center;
             font-weight: bold;
            font-size: 12px;
            margin: 12px 0;
        }

        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 6px;
            margin-top: 12px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        .report-title {
    text-align: center;
    font-weight: bold;
    font-size: 14px;
    margin: 15px 0 5px 0;
}

.report-subtitle {
    text-align: center;
    font-size: 12px;
    margin-bottom: 15px;
}

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .label {
            width: 160px;
            font-weight: bold;
        }

        .box {
            border: 1px solid #000;
            padding: 8px;
            min-height: 45px;
            margin-bottom: 10px;
            text-align: justify;
        }

        .footer {
            margin-top: 30px;
            width: 100%;
        }

        .signature {
            width: 35%;
            text-align: center;
            float: right;
        }

        .signature .name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }

        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    <!-- ================= HEADER ================= -->
    <div class="header">
    <img
            src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('logo.png'))) }}"
            class="logo"
        >
         <h1>KLINIK ABQARY</h1>
        <p>Pusat Layanan Terapi & Tumbuh Kembang Anak</p>
        <p> Jl. Raden Saleh No. 9C Karang Tengah, Ciledug, Kota Tangerang </p>
        <p>Telp: 081-9811-131 | Email: abqarcdc@gmail.com</p>
    </div>

    <!-- ================= IDENTITAS ================= -->
    <p class="report-title">LAPORAN EVALUASI TERAPI ANAK</p>
    <div class="section-title">A. IDENTITAS ANAK</div>
    <table class="info-table">
        <tr>
            <td class="label">Nama Anak</td>
            <td>: {{ $data->registrasiAnak->profileAnak->nama_anak ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Tanggal Evaluasi</td>
            <td>: {{ optional($data->created_at)->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Total Sesi Terapi</td>
            <td>: {{ $data->total_sesi ?? '-' }} sesi</td>
        </tr>
    </table>

    <!-- ================= KOMPONEN PERILAKU ================= -->
    <div class="section-title">B. KOMPONEN PERILAKU</div>
    <div class="box">
        {{ $data->komponen_perilaku ?? '-' }}
    </div>

    <!-- ================= KONDISI AWAL ================= -->
    <div class="section-title">C. KONDISI AWAL</div>
    <div class="box">
        {{ $data->kondisi_awal ?? '-' }}
    </div>

    <!-- ================= KONDISI SAAT INI ================= -->
    <div class="section-title">D. KONDISI SAAT INI</div>
    <div class="box">
        {{ $data->kondisi_saat_ini ?? '-' }}
    </div>

    <!-- ================= KEMAMPUAN SEBELUMNYA ================= -->
    <div class="section-title">E. KEMAMPUAN SEBELUMNYA</div>
    <div class="box">
        {{ $data->kemampuan_sebelumnya ?? '-' }}
    </div>

    <!-- ================= PENINGKATAN KEMAMPUAN ================= -->
    <div class="section-title">F. PENINGKATAN KEMAMPUAN SAAT INI</div>
    <div class="box">
        {{ $data->peningkatan_kemampuan_saat_ini ?? '-' }}
    </div>

    <!-- ================= PROGRAM LANJUTAN ================= -->
    <div class="section-title">G. PROGRAM LANJUTAN</div>
    <div class="box">
        {{ $data->program_lanjutan ?? '-' }}
    </div>

    <!-- ================= KESIMPULAN ================= -->
    <div class="section-title">H. KESIMPULAN FOLLOW UP</div>
    <div class="box">
        {{ $data->kesimpulan_hasil_followup ?? '-' }}
    </div>

    <!-- ================= SARAN ================= -->
    <div class="section-title">I. SARAN TERAPI</div>
    <div class="box">
        {{ $data->saran_terapi ?? '-' }}
    </div>

    <!-- ================= TANDA TANGAN ================= -->
    <div class="footer">
        <div class="signature">
            <p>{{ now()->format('d/m/Y') }}</p>
            <p>Terapis,</p>

            <div class="name">
                ( _______________________ )
            </div>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
