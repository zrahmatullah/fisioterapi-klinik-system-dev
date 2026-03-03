<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
    <title>Laporan Histori Promosi</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 11px; }
        .logo {
            position: absolute;
            left: 50;
            top:0;
            width: 140px;
        }
        .header {text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 18px;
            margin-bottom: 20px;
        }
        .header-text { flex: 1; text-align: center;}
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 14px;
        }

        .line {border-top: 2px solid #000;margin: 8px 0 12px 0;}
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f0f0f0; text-align: center; }
        h2 {
            text-align: center;
            margin-bottom: 6px;
        }
        .meta {text-align: center;margin-bottom: 12px; font-size: 11px;}
        .footer { margin-top: 30px; text-align: right; }
    </style>
</head>
<body>

<div class="header">
        <img
            src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('logo.png'))) }}"
            class="logo"
        >
    <div class="header-text">
        <h1>KLINIK ABQARY</h1>
        <p>Pusat Layanan Terapi & Tumbuh Kembang Anak</p>
        <p>
            Jl. Raden Saleh No 9 C Karang Tengah, Ciledug, Kota Tangerang<br>
            Telp: 081-9811-131 | Email: abqarcdc@gmail.com
        </p>
    </div>
</div>


<h2>LAPORAN HISTORI PROMOSI</h2>

<div class="meta">
    Periode: {{ $periode }}
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Promosi</th>
            <th>Nama Promosi</th>
            <th>Nominal Promosi</th>
            <th>Isi Promosi</th>
            <th>Tanggal Promo</th>
            <th>Waktu Promo</th>
            <th>Dipakai</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $i => $r)
        <tr>
            <td align="center">{{ $i + 1 }}</td>
            <td>{{ $r->kode_promo }}</td>
            <td>{{ $r->nama_promo }}</td>
            <td align="center">
                {{ $r->tipe_diskon === 'persen'
                    ? $r->nilai_diskon . ' %'
                    : number_format($r->nilai_diskon, 0, ',', '.') }}
            </td>
            <td>{{ $r->deskripsi }}</td>
            <td align="center">
                {{ \Carbon\Carbon::parse($r->tanggal_mulai)->format('d/m/Y') }}
                -
                {{ \Carbon\Carbon::parse($r->tanggal_selesai)->format('d/m/Y') }}
            </td>
            <td align="center">
                {{ \Carbon\Carbon::parse($r->created_at)->format('H:i') }}
            </td>
            <td align="center">{{ $r->total_dipakai }} x</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" align="center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Tanggal Cetak: {{ $tanggalCetak }}<br><br>
    Mengetahui,<br><br><br>
    <b>( Ryan Bagus )</b>
</div>

</body>
</html>
