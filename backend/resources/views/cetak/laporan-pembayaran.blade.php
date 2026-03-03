<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
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
            padding-bottom: 18px;
            margin-bottom: 15px;
        }

        .logo {
            position: absolute;
            left: 50;
            top:0;
            width: 150px;
        }

        .header h1 {
            margin: 0;
            font-size: 30px;
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
          margin-top: 30px; text-align: right
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


<h2>LAPORAN TRANSAKSI PEMBAYARAN</h2>

<div class="meta">
  Periode: {{ $periode }}
</div>

<table>
  <thead>
    <tr>
      <th>No</th>
      <th>No Invoice</th>
      <th>Nama Pasien</th>
      <th>Tanggal Bayar</th>
      <th>Kategori Layanan</th>
      <th>Jenis Layanan</th>
      <th>Sesi</th>
      <th>Sub Total</th>
      <th>Total</th>
    </tr>
  </thead>

  <tbody>
    @php $grandTotal = 0; @endphp

    @forelse($rows as $i => $r)
      @php $grandTotal += $r->jumlah_bayar; @endphp
      <tr>
        <td class="text-center">{{ $i + 1 }}</td>
        <td>{{ $r->nomor_pembayaran }}</td>
        <td>{{ $r->nama_anak }}</td>
        <td class="text-center">
          {{ \Carbon\Carbon::parse($r->tanggal_bayar)->format('d/m/Y') }}
        </td>
        <td>{{ $r->kategori_layanan }}</td>
        <td>{{ $r->layanan }}</td>
        <td class="text-center">{{ $r->jumlah_sesi }}</td>
        <td class="text-right">
          {{ number_format($r->total_tagihan, 0, ',', '.') }}
        </td>
        <td class="text-right">
          {{ number_format($r->jumlah_bayar, 0, ',', '.') }}
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="9" class="text-center">
          Tidak ada data
        </td>
      </tr>
    @endforelse
  </tbody>

  <tfoot>
    <tr>
      <th colspan="8" class="text-right">GRAND TOTAL</th>
      <th class="text-right">
        {{ number_format($grandTotal, 0, ',', '.') }}
      </th>
    </tr>
  </tfoot>
</table>

<div class="footer">
  Tanggal Cetak: {{ $tanggalCetak }}<br><br>
  Mengetahui,<br><br><br>
  <b>(                 )</b>
</div>

</body>
</html>
