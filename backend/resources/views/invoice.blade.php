<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $registrasi->no_regis }}</title>

<style>
@page{
    size:A4;
    margin:2.5cm 2.2cm 2.5cm 2.2cm;
}

body{
    font-family:"Times New Roman", serif;
    font-size:12pt;
    color:#000;
    margin:0;
    padding:0;
}

.container{
    width:100%;
}

/* ===== HEADER ===== */
.header{
    width:100%;
    border-bottom:3px solid #000;
    padding-bottom:10px;
    margin-bottom:18px;
}

.header table{
    width:100%;
}

.logo{
    width:80px;
}

.logo img{
    width:115px;
}
 

.clinic{
    text-align:right;
}

.clinic h2{
    margin:0;
    font-size:17pt;
    letter-spacing:1px;
}

.clinic p{
    margin:2px 0;
    font-size:10.5pt;
}

/* ===== TITLE ===== */
.title{
    text-align:center;
    margin:18px 0 14px 0;
}

.title h1{
    margin:0;
    font-size:18pt;
    letter-spacing:2px;
    text-decoration:underline;
}

/* ===== INFO TABLE ===== */
.info-table{
    width:100%;
    border-collapse:collapse;
    margin-bottom:16px;
}

.info-table td{
    padding:5px 4px;
    font-size:11.5pt;
}

/* ===== MAIN TABLE ===== */
.main-table{
    width:100%;
    border-collapse:collapse;
    margin-bottom:12px;
}

.main-table th,
.main-table td{
    border:1px solid #000;
    padding:7px 6px;
    font-size:11.5pt;
}

.main-table th{
    text-align:center;
    font-weight:bold;
}

.center{text-align:center;}
.right{text-align:right;}

.total-row td{
    font-weight:bold;
}

/* ===== FOOTER INFO ===== */
.terbilang{
    margin-top:8px;
    font-style:italic;
}

.payment-info{
    margin-top:6px;
}

/* ===== SIGNATURE ===== */
.signature-box{
    width:260px;
    float:right;
    margin-top:50px;
    text-align:center;
}

.clearfix{clear:both;}
</style>
</head>

<body>

@php
    $pembayaran = $registrasi->pembayarans->last();
    function terbilang($angka) {
        $angka = abs($angka);
        $satuan = ["", "satu","dua","tiga","empat","lima","enam","tujuh","delapan","sembilan","sepuluh","sebelas"];
        if($angka < 12) return $satuan[$angka];
        elseif($angka < 20) return terbilang($angka - 10)." belas";
        elseif($angka < 100) return terbilang(intval($angka / 10))." puluh ".terbilang($angka % 10);
        elseif($angka < 200) return "seratus ".terbilang($angka - 100);
        elseif($angka < 1000) return terbilang(intval($angka / 100))." ratus ".terbilang($angka % 100);
        elseif($angka < 2000) return "seribu ".terbilang($angka - 1000);
        elseif($angka < 1000000) return terbilang(intval($angka / 1000))." ribu ".terbilang($angka % 1000);
        elseif($angka < 1000000000) return terbilang(intval($angka / 1000000))." juta ".terbilang($angka % 1000000);
        else return terbilang(intval($angka / 1000000000))." milyar ".terbilang($angka % 1000000000);
    }
    $totalTagihan = $registrasi->pelayanans->sum(fn($p) => $p->qty * $p->harga);
@endphp

<div class="container">

<!-- HEADER -->
<div class="header">
<table>
<tr>
<td class="logo">
    <img src="{{ public_path('logo.png') }}">
</td>
<td class="clinic">
    <h2>KLINIK ABQRAY</h2>
    <p>Jl. Raden Saleh No 9 C Karang Tengah, Ciledug, Kota Tangerang</p>
    <p>Telp: 081-9811-131 | abqarcdc@gmail.com</p>
</td>
</tr>
</table>
</div>

<!-- TITLE -->
<div class="title">
<h1>INVOICE PEMBAYARAN</h1>
<p>No Registrasi : {{ $registrasi->no_regis }}</p>
</div>

<!-- INFO -->
<table class="info-table">
<tr>
    <td width="18%">Nama Anak</td>
    <td width="32%">: {{ $registrasi->profileAnak->nama_anak }}</td>
    <td width="18%">Tanggal</td>
    <td width="32%">: {{ $pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') : '-' }}</td>
</tr>
<tr>
    <td>Ruangan</td>
    <td>: {{ $registrasi->ruangan->ruangan }}</td>
    <td>Terapis</td>
    <td>: {{ $registrasi->terapis->nama }}</td>
</tr>
</table>

<!-- MAIN TABLE -->
<table class="main-table">
<thead>
<tr>
    <th width="5%">No</th>
    <th width="32%">Layanan</th>
    <th width="20%">Terapis</th>
    <th width="8%">Qty</th>
    <th width="15%">Harga</th>
    <th width="20%">Subtotal</th>
</tr>
</thead>
<tbody>
@foreach ($registrasi->pelayanans as $i => $pelayanan)
<tr>
    <td class="center">{{ $i+1 }}</td>
    <td>{{ $pelayanan->layanan->layanan ?? '-' }}</td>
    <td>{{ $pelayanan->terapis->nama ?? '-' }}</td>
    <td class="center">{{ $pelayanan->qty }}</td>
    <td class="right">{{ number_format($pelayanan->harga, 0, ',', '.') }}</td>
    <td class="right">{{ number_format($pelayanan->qty * $pelayanan->harga, 0, ',', '.') }}</td>
</tr>
@endforeach

<tr class="total-row">
    <td colspan="5" class="right">TOTAL</td>
    <td class="right">{{ number_format($totalTagihan, 0, ',', '.') }}</td>
</tr>
</tbody>
</table>

<div class="terbilang">
Terbilang : {{ ucfirst(terbilang($totalTagihan)) }} rupiah
</div>

<div class="payment-info">
@if($pembayaran)
Nomor Pembayaran : {{ $pembayaran->nomor_pembayaran }}<br>
Metode Pembayaran : {{ $pembayaran->metode_pembayaran }}<br>
Status : LUNAS
@else
Belum ada pembayaran
@endif
</div>

<!-- SIGNATURE -->
<div class="signature-box">
<p>Mengetahui,</p>
<br><br><br>
<strong>( {{ auth()->check() ? auth()->user()->name : 'Terapis' }} )</strong>
</div>

<div class="clearfix"></div>

</div>
</body>
</html>
