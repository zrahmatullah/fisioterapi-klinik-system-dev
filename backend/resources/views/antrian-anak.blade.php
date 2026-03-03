<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial;
            text-align: center;
            font-size: 12px;
        }
        .nomor {
            font-size: 48px;
            font-weight: bold;
            margin: 15px 0;
        }
        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
    </style>
</head>
<body>

<h3>KLINIK TERAPI ANAK</h3>
<hr>

<div>Nomor Antrian</div>

<div class="nomor">
    {{ $data->no_antrian }}
</div>

<hr>

<div>
    <strong>{{ $data->profileAnak->nama_anak }}</strong>
</div>

<div>
    Ruangan: {{ $data->ruangan->ruangan }}
</div>

<div>
    Terapis: {{ $data->terapis->nama }}
</div>

<div>
    {{ \Carbon\Carbon::parse($data->tgl_regis)->format('d/m/Y') }}
</div>

<div>
    {{ now()->format('H:i') }}
</div>

<hr>

<div>
    Mohon menunggu hingga dipanggil
</div>

</body>
</html>
