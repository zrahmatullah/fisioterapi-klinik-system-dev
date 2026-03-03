<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Laporan Evaluasi Terapi</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
        }

        .container {
            width: 100%;
            padding: 20px;
        }
        .logo {
            position: absolute;
            left: 50;
            top:0;
            width: 150px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 14px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
            font-size: 13px;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #f0f0f0;
            text-align: center;
        }

        .info td {
            border: none;
            padding: 4px;
        }

        .section {
            margin-top: 14px;
        }

        .label {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .box {
            border: 1px solid #000;
            padding: 8px;
            min-height: 50px;
        }

        .ttd {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
    <img
            src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('logo.png'))) }}"
            class="logo"
        >
        <h2>KLINIK ABQARY</h2>
        <p><strong> Pusat Layanan Terapi & Tumbuh Kembang Anak</strong></p>
        <p> Jl. Raden Saleh No. 9C Karang Tengah, Ciledug, Kota Tangerang </p>
        <p>Telp: 081-9811-131 | Email: abqarcdc@gmail.com</p>
    </div>

    <!-- JUDUL -->
    <div class="title">
        HASIL LAPORAN EVALUASI TERAPI
    </div>

    <!-- INFO ANAK -->
    <table class="info">
        <tr>
            <td width="25%">Nama Anak</td>
            <td width="35%">: <strong>{{ $header->registrasi->profileAnak->nama_anak }}</strong></td>
            <td width="20%">Tanggal Evaluasi</td>
            <td width="20%">: {{ $header->created_at->toDateString() }}</td>
        </tr>
        <tr>
            <td>Total Sesi</td>
            <td colspan="3">: {{ $header->total_sesi }}</td>
        </tr>
    </table>

    <!-- TABEL DETAIL EVALUASI -->
    <div class="section">
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Komponen</th>
                    <th width="23%">Kondisi Awal</th>
                    <th width="23%">Kondisi Saat Ini</th>
                    <th width="24%">Program Lanjutan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluasi as $i => $e)
                <tr>
                    <td align="center">{{ $i + 1 }}</td>
                    <td>{{ $e->komponen_perilaku }}</td>
                    <td>{{ $e->kondisi_awal }}</td>
                    <td>{{ $e->kondisi_saat_ini }}</td>
                    <td>{{ $e->program_lanjutan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- HASIL & SARAN -->
    <div class="section">
        <div class="label">Kesimpulan Hasil Follow Up</div>
        <div class="box">{{ $header->kesimpulan_hasil_followup }}</div>
    </div>

    <div class="section">
        <div class="label">Kemampuan Sebelumnya</div>
        <div class="box">{{ $header->kemampuan_sebelumnya }}</div>
    </div>

    <div class="section">
        <div class="label">Peningkatan Kemampuan Saat Ini</div>
        <div class="box">{{ $header->peningkatan_kemampuan_saat_ini }}</div>
    </div>

    <div class="section">
        <div class="label">Saran Terapi</div>
        <div class="box">{{ $header->saran_terapi }}</div>
    </div>

    <div class="footer">
    <div class="footer-right">
        <div>Tanggal Cetak : {{ $tanggalCetak }}</div>
        <br><br>
        <div>Mengetahui,</div>
        <br><br><br>
        <div><b>( {{ Auth::user()->name }} )</b></div>
    </div>
</div>

</div>

</body>
</html>
