<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Pendaftaran</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .container {
            width: 100%;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 10px;
        }

        .top-info {
            width: 100%;
            margin-bottom: 12px;
        }

        .top-info td {
            padding: 4px;
            border: none;
            font-size: 11px;
        }

        .section-title {
            font-weight: bold;
            margin: 10px 0 5px;
        }

        table.form {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        table.form td {
            padding: 4px;
            vertical-align: top;
            border: none;
        }

        .label {
            font-size: 10px;
            margin-bottom: 2px;
        }

        .box {
            border: 1px solid #999;
            padding: 5px;
            min-height: 18px;
            background: #f8f9fa;
            border-radius: 3px;
        }

        .box-large {
            min-height: 40px;
        }

        .terms {
            border: 1px solid #999;
            padding: 6px;
            background: #f8f9fa;
            font-size: 10px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 25px;
            border-top: 1px solid #999;
            padding-top: 8px;
        }

        .ttd {
            margin-top: 20px;
            width: 100%;
        }

        .ttd td {
            border: none;
            padding-top: 30px;
        }
    </style>
</head>

<body>
<div class="container">

    {{-- TITLE --}}
    <div class="title">FORMULIR PENDAFTARAN</div>

    {{-- HEADER INFO --}}
    <table class="top-info">
        <tr>
            <td><strong>ID Pasien</strong> : {{ $data->id_pasien }}</td>
            <td><strong>ID Pendaftaran</strong> : {{ $data->no_regis }}</td>
            <td align="right"><strong>Tanggal Cetak</strong> : {{ now()->format('d/m/Y') }}</td>
        </tr>
    </table>

    {{-- 1. DATA DIRI ANAK --}}
    <div class="section-title">1. DATA DIRI ANAK</div>
    <table class="form">
        <tr>
            <td width="50%">
                <div class="label">Nama Anak:</div>
                <div class="box">{{ $data->profileAnak->nama_anak ?? '' }}</div>
            </td>
            <td width="50%">
                <div class="label">Jenis Kelamin:</div>
                <div class="box">{{ $data->profileAnak->jenisKelamin->nama ?? '' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Tempat & Tanggal Lahir:</div>
                <div class="box">
                    {{ $data->profileAnak->tempat_lahir ?? '' }},
                    {{ $data->profileAnak->tanggal_lahir ?? '' }}
                </div>
            </td>
            <td>
                <div class="label">Usia:</div>
                <div class="box">{{ $data->profileAnak->umur ?? '' }} Tahun</div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="label">Agama:</div>
                <div class="box">{{ $data->profileAnak->agama->nama ?? '' }}</div>
            </td>
        </tr>

    </table>

    {{-- 2. PERKEMBANGAN MOTORIK --}}
    <div class="section-title">2. PERKEMBANGAN MOTORIK ANAK</div>
    <table class="form">
        <tr>
            <td width="50%">
                <div class="label">Tengkurap (bulan):</div>
                <div class="box">&nbsp;</div>
            </td>
            <td width="50%">
                <div class="label">Duduk (bulan):</div>
                <div class="box">&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Merangkak (bulan):</div>
                <div class="box">&nbsp;</div>
            </td>
            <td>
                <div class="label">Berdiri (bulan):</div>
                <div class="box">&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Berjalan (bulan):</div>
                <div class="box">&nbsp;</div>
            </td>
        </tr>
    </table>

    {{-- 3. PERKEMBANGAN BAHASA --}}
    <div class="section-title">3. PERKEMBANGAN BAHASA ANAK</div>
    <table class="form">
        <tr>
            <td width="50%">
                <div class="label">Babbling / suara (bulan):</div>
                <div class="box">&nbsp;</div>
            </td>
            <td width="50%">
                <div class="label">Mulai mengucap kata (bulan):</div>
                <div class="box">&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Mulai menyusun kalimat (bulan):</div>
                <div class="box">&nbsp;</div>
            </td>
            <td>
                <div class="label">Respons terhadap panggilan:</div>
                <div class="box">&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="label">Catatan Tambahan Bahasa:</div>
                <div class="box box-large">&nbsp;</div>
            </td>
        </tr>
    </table>

    {{-- 4. KESEHATAN --}}
    <div class="section-title">4. KEMAMPUAN & KESEHATAN SAAT INI</div>
    <table class="form">
        <tr>
            <td>
                <div class="label">Kemampuan Saat Ini:</div>
                <div class="box box-large">{{ $data->kemampuan_saat_ini ?? '' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Keluhan Saat Ini:</div>
                <div class="box box-large">{{ $data->keluhan_saat_ini ?? '' }}</div>
            </td>
        </tr>
    </table>

    {{-- 5. DATA ORANG TUA --}}
    <div class="section-title">5. DATA ORANG TUA</div>
    <table class="form">
        <tr>
            <td width="50%">
                <div class="label">Nama Ayah:</div>
                <div class="box">{{ $data->nama_ayah ?? '' }}</div>
            </td>
            <td width="50%">
                <div class="label">Nama Ibu:</div>
                <div class="box">{{ $data->nama_ibu ?? '' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Nomor Telepon:</div>
                <div class="box">{{ $data->notelp ?? '' }}</div>
            </td>
            <td>
                <div class="label">Usia Saat Menikah (Ayah):</div>
                <div class="box">{{ $data->usia_saat_menikah ?? '' }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="label">Alamat Lengkap:</div>
                <div class="box box-large">{{ $data->alamat ?? '' }}</div>
            </td>
        </tr>
    </table>

    {{-- 6. INFORMASI ASSESSMENT --}}
    <div class="section-title">6. INFORMASI ASSESSMENT</div>
    <table class="form">
        <tr>
            <td width="50%">
                <div class="label">Tanggal Kedatangan Assessment:</div>
                <div class="box">{{ $data->tgl_regis ?? '' }}</div>
            </td>
            <td width="50%">
                <div class="label">Layanan yang Dipilih:</div>
                <div class="box">
                    {{ $data->pelayanans->pluck('layanan.nama')->implode(', ') ?? '' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- 7. SYARAT --}}
    <div class="section-title">7. SYARAT & KETENTUAN</div>
    <div class="terms">
        • Orang tua wajib mengisi data anak dengan lengkap dan benar.<br>
        • Pendaftaran ini adalah persetujuan untuk dilakukan assessment awal anak.<br>
        • Data anak dan orang tua dijaga kerahasiaannya.<br>
        • Orang tua bertanggung jawab memastikan anak hadir sesuai jadwal.<br><br>
        Orang tua memahami bahwa:<br>
        • Data yang diberikan adalah benar.<br>
        • Proses terapi mengikuti SOP klinik.<br>
        • Pembayaran & jadwal mengikuti kebijakan klinik.
    </div>

    {{-- TTD --}}
    <table class="ttd">
        <tr>
            <td width="50%">TTD Orang Tua:</td>
            <td width="50%" align="right"></td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        Dokumen ini dicetak otomatis oleh Sistem Informasi Klinik<br>
        Tanggal : {{ now()->format('d/m/Y') }} &nbsp;&nbsp;
        Admin : {{ auth()->user()->name ?? 'Admin' }}
    </div>

</div>
</body>
</html>
