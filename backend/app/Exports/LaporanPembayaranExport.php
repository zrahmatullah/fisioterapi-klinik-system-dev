<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanPembayaranExport implements
    FromQuery,
    WithEvents,
    WithCustomStartCell
{
    protected $startDate;
    protected $endDate;
    protected $total;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate   = Carbon::parse($endDate)->endOfDay();
    }

    /**
     * 🔹 DATA QUERY (AMAN & PASTI KEISI)
     */
    public function query()
    {
        return DB::table('pembayaran_registrasi as p')
            ->leftJoin('registrasi_anak as r', 'r.id', '=', 'p.registrasi_anak_id')
            ->leftJoin('profile_anak as pa', 'pa.id', '=', 'r.profile_anak_id')
            ->leftJoin('pelayanan_terapi_anak as pta', 'pta.registrasi_anak_id', '=', 'r.id')
            ->leftJoin('layanan as l', 'l.id', '=', 'pta.layanan_id')
            ->leftJoin('kategori_layanan as kl', 'kl.id', '=', 'l.kategori_layanan_id')
            ->where('p.status', 'lunas')
            ->whereBetween('p.tanggal_bayar', [$this->startDate, $this->endDate])
            ->groupBy(
                'p.id',
                'p.nomor_pembayaran',
                'p.tanggal_bayar',
                'pa.nama_anak',
                'kl.kategori_layanan',
                'l.layanan',
                'p.total_tagihan',
                'p.jumlah_bayar'
            )
            ->select(
                'p.nomor_pembayaran',
                'pa.nama_anak',
                'p.tanggal_bayar',
                'kl.kategori_layanan',
                'l.layanan',
                DB::raw('COUNT(pta.id) as jumlah_sesi'),
                'p.total_tagihan',
                'p.jumlah_bayar'
            )
            ->orderBy('p.tanggal_bayar', 'asc');
    }

    /**
     * 🔹 DATA MULAI DARI ROW 5 (SETELAH HEADER)
     */
    public function startCell(): string
    {
        return 'A5';
    }

    /**
     * 🔹 LAYOUT
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // JUDUL
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI PEMBAYARAN');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // PERIODE
                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue(
                    'A2',
                    'Periode: ' .
                        $this->startDate->format('d/m/Y') .
                        ' s/d ' .
                        $this->endDate->format('d/m/Y')
                );

                // HEADER TABLE (ROW 4)
                $headers = [
                    'No Invoice',
                    'Nama Pasien',
                    'Tanggal Bayar',
                    'Kategori Layanan',
                    'Jenis Layanan',
                    'Jumlah Sesi',
                    'Sub Total',
                    'Total'
                ];

                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($col . '4', $header);
                    $col++;
                }

                $sheet->getStyle('A4:H4')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'E5E7EB']
                    ],
                    'alignment' => ['horizontal' => 'center']
                ]);

                // LAST ROW SETELAH DATA
                $lastRow = $sheet->getHighestRow();

                // BORDER
                $sheet->getStyle("A4:H{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // FORMAT RUPIAH
                $sheet->getStyle("G5:H{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0');

                // GRAND TOTAL
                $this->total = DB::table('pembayaran_registrasi')
                    ->where('status', 'lunas')
                    ->whereBetween('tanggal_bayar', [$this->startDate, $this->endDate])
                    ->sum('jumlah_bayar');

                $totalRow = $lastRow + 1;
                $sheet->mergeCells("A{$totalRow}:G{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'GRAND TOTAL');
                $sheet->setCellValue("H{$totalRow}", $this->total);

                $sheet->getStyle("A{$totalRow}:H{$totalRow}")
                    ->getFont()
                    ->setBold(true);

                // AUTO WIDTH
                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}