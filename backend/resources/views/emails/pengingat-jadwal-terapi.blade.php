<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengingat Jadwal Terapi</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color:#333">

    <h3>Pengingat Jadwal Terapi</h3>

    <p>
        Halo Bapak/Ibu,<br><br>
        Kami ingin mengingatkan bahwa <b>{{ $registrasi->profileAnak->nama_anak }}</b>
        memiliki jadwal terapi yang akan segera dilaksanakan.
    </p>

    <p><b>Berikut detailnya:</b></p>

    <table cellpadding="6" cellspacing="0">
        <tr>
            <td width="160">Nama Anak</td>
            <td>: {{ $registrasi->profileAnak->nama_anak }}</td>
        </tr>
    </table>

    <br>

    {{-- ================= JADWAL TERAPI ================= --}}
    <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse">
        <thead style="background:#f3f4f6">
            <tr>
                <th align="left">Sesi</th>
                <th align="left">Terapi</th>
                <th align="left">Tanggal</th>
                <th align="left">Waktu</th>
                <th align="left">Ruangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registrasi->pelayanans as $i => $p)
                <tr>
                    <td>Sesi {{ $i + 1 }}</td>
                    <td>{{ $p->layanan->layanan ?? '-' }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($p->tanggal_penjadwalan)->translatedFormat('l, d F Y') }}
                    </td>
                    <td>
                        {{ $p->jam_mulai ?? '-' }} - {{ $p->jam_selesai ?? '-' }}
                    </td>
                    <td>
                        {{ $p->ruangan->ruangan ?? 'Terapi' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    <p>
        Kami tunggu kehadiran sesuai jadwal ya, Bapak/Ibu.<br>
        Apabila terdapat kendala, Bapak/Ibu dapat menghubungi admin kami untuk informasi lebih lanjut.
    </p>

    <br>

    <p>
        Terima kasih atas kepercayaannya 🙏<br><br>
        Salam hangat,<br>
        <b>Admin</b><br>
        Klinik Abqary
    </p>

</body>
</html>
