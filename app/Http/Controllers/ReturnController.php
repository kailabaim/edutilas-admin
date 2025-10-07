<?php

namespace App\Http\Controllers;

use App\Models\Returns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        // Query dari tabel returns
        $query = Returns::with(['book', 'kelas', 'student', 'guru']);
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('return_code', 'like', "%{$search}%")
                  ->orWhereHas('student', function($subQ) use ($search) {
                      $subQ->where('full_name', 'like', "%{$search}%")
                           ->orWhere('nis', 'like', "%{$search}%");
                  })
                  ->orWhereHas('guru', function($subQ) use ($search) {
                      $subQ->where('nama_guru', 'like', "%{$search}%")
                           ->orWhere('nip', 'like', "%{$search}%");
                  })
                  ->orWhereHas('book', function($subQ) use ($search) {
                      $subQ->where('title', 'like', "%{$search}%");
                  });
            });
        }
        
        // Condition filter
        if ($request->filled('status')) {
            $query->where('book_condition', $request->status);
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->date_to);
        }
        
        // Get monthly statistics
        $monthlyStats = $this->getMonthlyStatistics();
        
        // Pagination
         // Get all returns
       $returns = $query->orderBy('return_date', 'desc')->paginate(10);
        return view('dashboard.peminjaman', compact('returns', 'monthlyStats'));
    }
    
    /**
     * Get monthly return statistics for current year
     */
    private function getMonthlyStatistics()
    {
        $currentYear = date('Y');
        
        $stats = Returns::select(
                DB::raw('MONTH(return_date) as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN book_condition = "good" THEN 1 ELSE 0 END) as good_condition'),
                DB::raw('SUM(CASE WHEN book_condition = "damaged" THEN 1 ELSE 0 END) as damaged_condition'),
                DB::raw('SUM(CASE WHEN book_condition = "lost" THEN 1 ELSE 0 END) as lost_condition'),
                DB::raw('SUM(days_late) as total_days_late'),
                DB::raw('SUM(fine_amount) as total_fines')
            )
            ->whereYear('return_date', $currentYear)
            ->groupBy(DB::raw('MONTH(return_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');
        
        // Fill missing months with zero values
        $monthlyData = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = [
                'month' => $monthNames[$i - 1],
                'month_number' => $i,
                'total' => $stats->has($i) ? $stats[$i]->total : 0,
                'good' => $stats->has($i) ? $stats[$i]->good_condition : 0,
                'damaged' => $stats->has($i) ? $stats[$i]->damaged_condition : 0,
                'lost' => $stats->has($i) ? $stats[$i]->lost_condition : 0,
                'days_late' => $stats->has($i) ? $stats[$i]->total_days_late : 0,
                'fines' => $stats->has($i) ? $stats[$i]->total_fines : 0,
            ];
        }
        
        return [
            'year' => $currentYear,
            'data' => $monthlyData,
            'total_returns' => array_sum(array_column($monthlyData, 'total')),
            'total_fines' => array_sum(array_column($monthlyData, 'fines')),
        ];
    }
    
    /**
     * Export returns data to Excel
     */
    public function export(Request $request)
    {
        $query = Returns::with(['book', 'kelas', 'student', 'guru']);        
        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('return_code', 'like', "%{$search}%")
                  ->orWhereHas('student', function($subQ) use ($search) {
                      $subQ->where('full_name', 'like', "%{$search}%")
                           ->orWhere('nis', 'like', "%{$search}%");
                  })
                  ->orWhereHas('guru', function($subQ) use ($search) {
                      $subQ->where('nama_guru', 'like', "%{$search}%")
                           ->orWhere('nip', 'like', "%{$search}%");
                  })
                  ->orWhereHas('book', function($subQ) use ($search) {
                      $subQ->where('title', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('book_condition', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->date_to);
        }
        
        $returns = $query->orderBy('return_date', 'desc')->get();
        
        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SMK Negeri 13 Bandung')
            ->setTitle('Data Pengembalian Buku')
            ->setSubject('Laporan Pengembalian')
            ->setDescription('Data pengembalian buku perpustakaan');
        
        // Header styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        
        // Set headers
        $headers = [
            'No',
            'Kode Pengembalian',
            'Nama Peminjam',
            'NIS/NIP',
            'Kelas/Status',
            'Judul Buku',
            'Tanggal Kembali',
            'Hari Terlambat',
            'Denda (Rp)',
            'Kondisi Buku',
            'Catatan'
        ];
        
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
        
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(35);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(30);
        
        // Fill data
        $row = 2;
        $no = 1;
        foreach ($returns as $return) {
            $peminjam = $return->student 
                ? $return->student->full_name 
                : ($return->guru ? $return->guru->nama_guru : 'N/A');
            
            $nisNip = $return->student 
                ? $return->student->nis 
                : ($return->guru ? $return->guru->nip : 'N/A');
            
            $kelasStatus = $return->student 
                ? ($return->kelas ? $return->kelas->class_name : '-')
                : 'Guru';
            
            $kondisi = match($return->book_condition) {
                'good' => 'Baik',
                'damaged' => 'Rusak',
                'lost' => 'Hilang',
                default => 'N/A'
            };
            
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $return->return_code);
            $sheet->setCellValue('C' . $row, $peminjam);
            $sheet->setCellValue('D' . $row, $nisNip);
            $sheet->setCellValue('E' . $row, $kelasStatus);
            $sheet->setCellValue('F' . $row, $return->book->title ?? 'N/A');
            $sheet->setCellValue('G' . $row, date('d/m/Y', strtotime($return->return_date)));
            $sheet->setCellValue('H' . $row, $return->days_late);
            $sheet->setCellValue('I' . $row, $return->fine_amount);
            $sheet->setCellValue('J' . $row, $kondisi);
            $sheet->setCellValue('K' . $row, $return->notes ?? '-');
            
            $row++;
        }
        
        // Add borders to data
        $sheet->getStyle('A1:K' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // Summary section
        $row += 2;
        $sheet->setCellValue('A' . $row, 'RINGKASAN:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Pengembalian:');
        $sheet->setCellValue('B' . $row, $returns->count());
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Denda:');
        $sheet->setCellValue('B' . $row, 'Rp ' . number_format($returns->sum('fine_amount'), 0, ',', '.'));
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Kondisi Baik:');
        $sheet->setCellValue('B' . $row, $returns->where('book_condition', 'good')->count());
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Kondisi Rusak:');
        $sheet->setCellValue('B' . $row, $returns->where('book_condition', 'damaged')->count());
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Kondisi Hilang:');
        $sheet->setCellValue('B' . $row, $returns->where('book_condition', 'lost')->count());
        
        // Generate filename
        $filename = 'Data_Pengembalian_Buku_' . date('Y-m-d_His') . '.xlsx';
        
        // Create writer and download
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
