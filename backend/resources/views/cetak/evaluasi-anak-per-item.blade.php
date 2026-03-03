<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Laporan Evaluasi Terapi</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .logo {
            position: absolute;
            left: 50;
            top:0;
            width: 140px;
        }
        
        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
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
        }

        .info td {
            padding: 4px;
            vertical-align: top;
        }

        .table-evaluasi th,
        .table-evaluasi td {
            border: 1px solid #000;
            padding: 5px;
        }

        .table-evaluasi th {
            background: #f2f2f2;
            text-align: center;
        }

        .section {
            margin-top: 15px;
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
        <p>Jl. Raden Saleh No 9 karang tengah ciledug, Kota Tangerang</p>
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
            <td width="35%">: <strong>{{ $evaluasi->registrasi->profileAnak->nama_anak }}</strong></td>
            <td width="20%">Terapis</td>
            <td width="20%">: -</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>: {{ $evaluasi->registrasi->profileAnak->jenisKelamin->nama ?? '-' }}</td>
            <td>Kategori Layanan</td>
            <td>:{{ optional(optional($evaluasi->registrasi->pelayananTerapiAnak)->layanan)->layanan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tempat & Tgl Lahir</td>
            <td colspan="3">
                : {{ $evaluasi->registrasi->profileAnak->tempat_lahir ?? '-' }},
                {{ $evaluasi->registrasi->profileAnak->tanggal_lahir ?? '-' }}
            </td>
        </tr>
    </table>

    <!-- TABEL EVALUASI -->
    <div class="section">
        <table class="table-evaluasi">
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
                <tr>
                    <td style="text-align:center;">1</td>
                    <td>{{ $evaluasi->komponen_perilaku }}</td>
                    <td>{{ $evaluasi->kondisi_awal }}</td>
                    <td>{{ $evaluasi->kondisi_saat_ini }}</td>
                    <td>{{ $evaluasi->program_lanjutan }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- HASIL & SARAN -->
    <div class="section">
        <div class="label">Kesimpulan Hasil Follow Up</div>
        <div class="box">{{ $evaluasi->kesimpulan_hasil_followup }}</div>
    </div>

    <div class="section">
        <div class="label">Kemampuan Sebelumnya</div>
        <div class="box">{{ $evaluasi->kemampuan_sebelumnya }}</div>
    </div>

    <div class="section">
        <div class="label">Peningkatan Kemampuan Saat Ini</div>
        <div class="box">{{ $evaluasi->peningkatan_kemampuan_saat_ini }}</div>
    </div>

    <div class="section">
        <div class="label">Saran Terapi</div>
        <div class="box">{{ $evaluasi->saran_terapi }}</div>
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
