<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Returns;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik utama
        $totalBooks = Book::count();
        $activeBorrows = Loan::where('status', 'active')->count();

        // PERUBAHAN UTAMA: Ambil semua transaksi diurutkan dari TERLAMA
        // Menggunakan orderBy created_at ASC untuk data terlama dulu
        $recentTransactions = Loan::with(['book.categoryRelation','student.kelas','guru'])
            ->orderBy('created_at', 'ASC')  // Dari terlama ke terbaru
            ->get();

        // Analisis Bulanan - Menggunakan created_at karena lebih konsisten
        $monthly = Loan::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', now()->year)  // Hanya tahun ini
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Generate array bulan (1-12) dengan data 0 jika tidak ada transaksi
        $months = [];
        $totalsMonthly = [];
        $monthlyData = $monthly->keyBy('month');
        
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date("M", mktime(0, 0, 0, $i, 1)); // Format singkat (Jan, Feb, Mar)
            $totalsMonthly[] = isset($monthlyData[$i]) ? (int)$monthlyData[$i]->total : 0;
        }

        // Analisis Tahunan - Menggunakan created_at
        $yearly = Loan::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->get();

        $years = [];
        $totalsYearly = [];
        foreach ($yearly as $row) {
            $years[] = (string)$row->year;
            $totalsYearly[] = (int)$row->total;
        }

        // Jika tidak ada data tahunan, berikan data default dengan tahun saat ini
        if (empty($years)) {
            $years = [date('Y')];
            $totalsYearly = [0];
        }

        return view('dashboard.index', compact(
            'totalBooks',
            'activeBorrows',
            'recentTransactions',
            'months',
            'totalsMonthly',
            'years',
            'totalsYearly'
        ));
    }

    /**
     * Method untuk menandai peminjaman sebagai dikembalikan
     * Data akan dipindah dari loans ke returns table
     */
    public function markAsReturned(Request $request, $id)
    {
        try {
            // Mulai database transaction
            DB::beginTransaction();

            // Ambil data loan
            $loan = Loan::with(['student.kelas', 'guru', 'book.categoryRelation'])->findOrFail($id);

            // Generate return code
            $returnCode = 'RET-' . date('YmdHis') . '-' . str_pad($loan->loan_id, 4, '0', STR_PAD_LEFT);

            // Validasi input dari form modal (optional)
            $validated = $request->validate([
                'book_condition' => 'required|in:good,damaged,lost',
                'fine_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:500',
            ]);

            // Hitung denda default jika tidak diinputkan manual (Rp 1000 per hari)
            $fine = 0;
            $daysLate = 0;
            if ($loan->due_date && now() > $loan->due_date) {
                $daysLate = now()->diffInDays($loan->due_date);
                $fine = $daysLate * 1000; // Rp 1000 per hari
            }

            // Jika admin mengisi denda manual, gunakan nilai tersebut
            if ($request->filled('fine_amount')) {
                $fine = (float) $validated['fine_amount'];
            }

            // Debug: Log data yang akan disimpan
            \Log::info('Creating return record', [
                'loan_id' => $loan->loan_id,
                'book_id' => $loan->book_id,
                'student_id' => $loan->student_id,
                'guru_id' => $loan->guru_id,
                'kelas_id' => $loan->kelas_id
            ]);

            // Buat record di tabel returns dengan field yang sesuai
            $returnData = [
                'return_code' => $returnCode,
                'loan_id' => $loan->loan_id,
                'book_id' => $loan->book_id,
                'return_date' => now()->toDateString(),
                'days_late' => $daysLate,
                'fine_amount' => $fine,
                'book_condition' => $validated['book_condition'] ?? 'good',
                'notes' => $validated['notes'] ?? 'Dikembalikan melalui dashboard admin',
                'kelas_id' => $loan->kelas_id,
                'student_id' => $loan->student_id,
                'guru_id' => $loan->guru_id
            ];

            // Update stok buku (kembalikan 1 unit)
            $book = Book::findOrFail($loan->book_id);
            $book->increment('available_copies');

            // Buat record di tabel returns dengan loan_id yang valid
            $return = Returns::create($returnData);

            // Debug: Log hasil create
            \Log::info('Return record created', ['return_id' => $return->id]);

            // Hapus record dari tabel loans setelah returns dibuat
            $loan->delete();

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Buku berhasil dikembalikan dan data dipindah ke tabel returns.',
                'data' => [
                    'return_code' => $returnCode,
                    'fine_amount' => $fine,
                    'days_late' => $daysLate,
                    'book_title' => $book->title,
                    'available_copies' => $book->available_copies,
                    'return_id' => $return->id
                ]
            ]);

        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollback();

            // Log error untuk debugging
            \Log::error('Error in markAsReturned', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengembalikan buku: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Method untuk mendapatkan statistik detail
     */
    public function getStats()
    {
        $stats = [
            'total_books' => Book::count(),
            'active_loans' => Loan::where('status', 'active')->count(),
            'returned_books' => Returns::count(), // Update: Hitung dari tabel returns
            'overdue_books' => Loan::where('status', 'overdue')->count(),
            'today_loans' => Loan::whereDate('created_at', today())->count(),
            'this_month_loans' => Loan::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Method untuk mendapatkan transaksi terbaru (AJAX)
     */
    public function getRecentTransactions()
    {
        $transactions = Loan::with(['student.kelas', 'guru', 'book.categoryRelation'])
            ->orderBy('created_at', 'DESC')  // Untuk AJAX, yang terbaru dulu
            ->take(10)
            ->get();

        return response()->json($transactions);
    }

    /**
     * Method untuk mendapatkan detail transaksi (AJAX)
     */
    public function getTransactionDetail($id)
    {
        try {
            $transaction = Loan::with(['student.kelas', 'guru', 'book.categoryRelation'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $transaction
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * Method untuk menghitung dan menambah denda
     */
    public function calculateFine($id)
    {
        try {
            $transaction = Loan::findOrFail($id);
            
            // Hitung denda jika terlambat (misalnya Rp 1000 per hari)
            $fine = 0;
            $daysLate = 0;
            if ($transaction->due_date && now() > $transaction->due_date) {
                $daysLate = now()->diffInDays($transaction->due_date);
                $fine = $daysLate * 1000; // Rp 1000 per hari
            }

            return response()->json([
                'success' => true,
                'fine' => $fine,
                'days_late' => $daysLate
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung denda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Method untuk melihat history returns
     */
    public function getReturnsHistory(Request $request)
    {
        try {
            $query = Returns::with(['book.categoryRelation', 'student.kelas', 'guru']);

            // Filter berdasarkan tanggal jika ada
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('return_date', [$request->start_date, $request->end_date]);
            }

            // Filter berdasarkan kondisi buku
            if ($request->has('book_condition') && $request->book_condition != '') {
                $query->where('book_condition', $request->book_condition);
            }

            $returns = $query->orderBy('return_date', 'desc')->paginate(50);

            return response()->json([
                'success' => true,
                'data' => $returns
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data history returns: ' . $e->getMessage()
            ], 500);
        }
    }
}