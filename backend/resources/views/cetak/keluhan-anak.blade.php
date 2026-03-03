<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Keluhan Anak</title>

    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header img {
            height: 55px;
            float: left;
        }

        .header .title {
            font-size: 16px;
            font-weight: bold;
        }

        .header .subtitle {
            font-size: 11px;
        }

        h3 {
            text-align: center;
            margin: 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 4px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 25%;
        }

        .section {
            margin-bottom: 12px;
        }

        .box {
            border: 1px solid #000;
            padding: 10px;
            min-height: 120px;
        }

        /* ===== CHECKBOX AMAN DOMPDF ===== */
        .cb {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #000;
            text-align: center;
            line-height: 14px;
            font-size: 11px;
            font-weight: bold;
            margin-right: 6px;
        }

        .row {
            margin-bottom: 6px;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }
    </style>
</head>
<body>

{{-- ================= HEADER ================= --}}
<div class="header">
    <img src="{{ public_path('logo.png') }}" alt="-">
    <div class="title"> KLINIK ABQARY</div>
    <div class="subtitle">
        Pusat Layanan Terapi & Tumbuh Kembang Anak <br>
        Jl. Raden Saleh No. 9C Karang Tengah, Ciledug, Kota Tangerang <br>
        Telp: 0811-9811-131 | Email: abqarycdc@gmail.com
    </div>
    <div style="clear:both;"></div>
</div>

<h3>DATA KELUHAN</h3>

{{-- ================= INFORMASI ANAK & ORANG TUA ================= --}}
<div class="section">
    <table>
        <tr>
            <td class="label">Nama Anak</td>
            <td>: {{ $keluhan->anak->nama_anak }}</td>
            <td class="label">Nama Orang Tua</td>
            <td>: {{ $keluhan->anak->orangTua->nama ?? '-' }}</td>
        </tr>
    </table>
</div>

{{-- ================= KATEGORI KELUHAN ================= --}}
@php
    $kategori = $keluhan->kategori_keluhan;
@endphp

<div class="section">
    <table>
        <tr>
            <td class="label">Kategori Keluhan</td>
            <td>
                <div class="row">
                    <span class="cb">{{ $kategori === 'Pelayanan Administrasi' ? 'V' : '' }}</span>
                    Pelayanan Administrasi

                    &nbsp;&nbsp;&nbsp;

                    <span class="cb">{{ $kategori === 'Kualitas Terapi' ? 'V' : '' }}</span>
                    Kualitas Terapi
                </div>

                <div class="row">
                    <span class="cb">{{ $kategori === 'Sarana & Prasarana' ? 'V' : '' }}</span>
                    Sarana & Prasarana

                    &nbsp;&nbsp;&nbsp;

                    <span class="cb">{{ $kategori === 'Jadwal / Waktu Pelayanan' ? 'V' : '' }}</span>
                    Jadwal / Waktu Pelayanan
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- ================= INFORMASI KELUHAN ================= --}}
<div class="section">
    <table>
        <tr>
            <td class="label">Tanggal Keluhan</td>
            <td>
                : {{ \Carbon\Carbon::parse($keluhan->tanggal_keluhan)->format('d/m/Y') }}
            </td>
            <td class="label">No Keluhan</td>
            <td>: {{ $keluhan->no_keluhan }}</td>
        </tr>
    </table>
</div>

{{-- ================= ISI KELUHAN ================= --}}
<div class="section">
    <div class="label">Isi Keluhan</div>
    <div class="box">
        {{ $keluhan->isi }}
    </div>
</div>

{{-- ================= FOOTER ================= --}}
<div class="footer">
    <div>Tanggal Cetak: {{ now()->format('d/m/Y') }}</div>
    <br><br>
    <div>Mengetahui,</div>
    <br><br><br>
    <div><strong>( Admin )</strong></div>
</div>

</body>
</html>
