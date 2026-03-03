<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
    <title>Laporan Keluhan</title>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11px;
            margin: 20px;
        }

        /* ===== HEADER SURAT ===== */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
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


        h2 {
            text-align: center;
            margin-bottom: 6px;
        }

        .meta {
            margin-bottom: 12px;
        }

        .meta div {
            margin-bottom: 4px;
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

        td.center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            width: 100%;
        }

        .footer-right {
            width: 40%;
            float: right;
            text-align: center;
        }
    </style>
</head>
<body>

        <!-- ===== HEADER ===== -->
        <div class="header">
        <img
            src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('logo.png'))) }}"
            class="logo"
        >
        <h1>KLINIK ABQARY</h1>
        <p>Pusat Layanan Terapi & Tumbuh Kembang Anak</p>
        <p>
        Jl. Raden Saleh No 9 C Karang Tengah, Ciledug, Kota Tangerang<br>
        Telp: 081-9811-131 | Email: abqarcdc@gmail.com
    </p>
</div>

    <h2>LAPORAN KELUHAN</h2>

    <div class="meta" style="text-align:center;">
    <div><b>Periode</b> : {{ $periode }}</div>
</div>


    <table>
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:15%">Nomor Keluhan</th>
                <th style="width:15%">Nama Anak</th>
                <th style="width:15%">Kategori Keluhan</th>
                <th style="width:10%">Tanggal Keluhan</th>
                <th style="width:20%">Isi Keluhan</th>
                <th style="width:10%">Status</th>
                <th style="width:20%">Isi Tanggapan</th>
                <th style="width:10%">Tanggal Tanggapan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $i => $r)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $r->no_keluhan }}</td>
                    <td>{{ $r->nama_anak ?? '-' }}</td>
                    <td>{{ $r->kategori_keluhan ?? '-' }}</td>
                    <td class="center">
                        {{ \Carbon\Carbon::parse($r->tanggal_keluhan)->format('d/m/Y') }}
                    </td>
                    <td>{{ $r->isi }}</td>
                    <td class="center">{{ $r->status_tanggapan }}</td>
                    <td>{{ $r->tanggapan_keluhan ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->updated_at)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-right">
            <div>Tanggal Cetak : {{ $tanggalCetak }}</div>
            <br><br>
            <div>Mengetahui,</div>
            <br><br><br>
            <div><b>( Ryan Bagus )</b></div>
        </div>
    </div>

</body>
</html>
