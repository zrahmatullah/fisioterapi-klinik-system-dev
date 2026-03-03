<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Catatan Aktivitas Terapi Anak</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header .title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .sub-title {
            font-size: 10px;
            margin-top: 2px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .label {
            width: 18%;
            font-weight: bold;
        }

        .colon {
            width: 2%;
        }

        .value {
            width: 30%;
        }

        .section-title {
            font-weight: bold;
            margin: 10px 0 5px;
            text-transform: uppercase;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        table.data-table th {
            text-align: center;
            font-weight: bold;
            background: #f2f2f2;
        }

        .signature {
            width: 100%;
            margin-top: 25px;
        }

        .signature .right {
            width: 40%;
            float: right;
            text-align: center;
        }

        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="title">Catatan Aktivitas Terapi Anak</div>
        <div class="sub-title">Laporan Sesi Terapi Per Anak</div>
    </div>

    <!-- INFO -->
    <table class="info-table">
        <tr>
            <td class="label">Nama Anak</td>
            <td class="colon">:</td>
            <td class="value">{{ $data->registrasiAnak->profileAnak->nama_anak ?? '-' }}</td>

            <td class="label">Status</td>
            <td class="colon">:</td>
            <td class="value">{{ $data->checkin_sesi ? 'Hadir' : 'Terjadwal' }}</td>
        </tr>
        <tr>
            <td class="label">Layanan</td>
            <td class="colon">:</td>
            <td class="value">{{ $data->pelayananTerapiAnak->layanan->layanan ?? '-' }}</td>

            <td class="label">Terapis</td>
            <td class="colon">:</td>
            <td class="value">{{ $data->pelayananTerapiAnak->terapis->nama ?? '-' }}</td>
        </tr>
    </table>

    <!-- DATA -->
    <div class="section-title">Detail Aktivitas</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:30%">Aktivitas Terapi</th>
                <th style="width:30%">Keterangan Terapi</th>
                <th style="width:35%">Tugas Rumah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">1</td>
                <td>{{ $data->aktivitas_terapi ?? '-' }}</td>
                <td>{{ $data->keterangan_terapi ?? '-' }}</td>
                <td>{{ $data->tugas_rumah ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="signature">
        <div class="right">
            Terapis,<br><br><br><br>
            <u>{{ $data->pelayananTerapiAnak->terapis->nama ?? '.........................' }}</u>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
