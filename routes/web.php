<?php 

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\DataWargaSekolahController;
use App\Models\Admin;

Route::get('/', function () {
    return redirect()->route('login');
});

// Login routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'username' => ['required','string'],
        'password' => ['required','string'],
    ]);

    $identifier = $credentials['username'];
    $admin = Admin::where('email', $identifier)
        ->orWhere('username', $identifier)
        ->first();

    if (!$admin) {
        return back()->with('status', 'Akun admin tidak ditemukan.');
    }

    if (!Hash::check($credentials['password'], $admin->password)) {
        return back()->with('status', 'Password salah.');
    }

    Auth::guard('admin')->login($admin, $request->boolean('remember'));
    $request->session()->regenerate();
    return redirect()->route('dashboard');
})->middleware('web')->name('login.attempt');

// Dashboard & menu (wajib login admin)
Route::middleware('auth:admin')->group(function () {
    // ===== DASHBOARD ROUTES =====
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // AJAX endpoints untuk dashboard
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/recent-transactions', [DashboardController::class, 'getRecentTransactions'])->name('dashboard.recent-transactions');
    
    // Routes untuk fitur return book dari dashboard
    Route::post('/dashboard/return/{id}', [DashboardController::class, 'markAsReturned'])->name('dashboard.return');
    Route::get('/dashboard/transactions/{id}/detail', [DashboardController::class, 'getTransactionDetail'])->name('dashboard.transactions.detail');
    Route::post('/dashboard/calculate-fine/{id}', [DashboardController::class, 'calculateFine'])->name('dashboard.calculate-fine');
    
    // Route untuk returns history
    Route::get('/dashboard/returns-history', [DashboardController::class, 'getReturnsHistory'])->name('dashboard.returns-history');
    
    // ===== MANAJEMEN BUKU ROUTES =====
    Route::get('/dashboard/buku', [BookController::class, 'index'])->name('dashboard.buku');
    
    Route::prefix('dashboard/buku')->name('dashboard.buku.')->group(function () {
        Route::get('/create', [BookController::class, 'create'])->name('create');
        Route::post('/', [BookController::class, 'store'])->name('store');
        
        // Route baru untuk mendapatkan detail buku via AJAX
        Route::get('/{id}/detail', [BookController::class, 'getDetail'])->name('detail');
        
        Route::get('/{id}', [BookController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BookController::class, 'edit'])->name('edit');
        
        // Route untuk update dan delete buku
        Route::put('/{id}', [BookController::class, 'update'])->name('update');
        Route::delete('/{id}', [BookController::class, 'destroy'])->name('destroy');
        
        Route::post('/import', [BookController::class, 'import'])->name('import');
        Route::get('/export', [BookController::class, 'export'])->name('export');
    });
    
    // ===== PENGEMBALIAN BUKU ROUTES (ReturnController) =====
    Route::prefix('dashboard/peminjaman')->name('dashboard.peminjaman')->group(function () {
        // Main index route
        Route::get('/', [ReturnController::class, 'index']);
        
        // Export Excel route
        Route::get('/export', [ReturnController::class, 'export'])->name('.export');
    });

    // ===== DATA WARGA SEKOLAH ROUTES =====
    Route::get('/dashboard/data-warga-sekolah', [DataWargaSekolahController::class, 'index'])->name('dashboard.data-warga-sekolah');

    // AJAX routes untuk Data Warga Sekolah
    Route::prefix('dashboard/api/data-warga-sekolah')->name('dashboard.api.data-warga-sekolah.')->group(function () {
        Route::get('/students', [DataWargaSekolahController::class, 'getStudents'])->name('students.get');
        Route::get('/students/{id}', [DataWargaSekolahController::class, 'getStudentDetail'])->name('students.detail');
        Route::get('/teachers', [DataWargaSekolahController::class, 'getTeachers'])->name('teachers.get');
        Route::get('/teachers/{id}', [DataWargaSekolahController::class, 'getTeacherDetail'])->name('teachers.detail');
        Route::get('/stats', [DataWargaSekolahController::class, 'getStats'])->name('stats');
        Route::get('/class-options', [DataWargaSekolahController::class, 'getClassOptions'])->name('class-options');
        Route::get('/major-options', [DataWargaSekolahController::class, 'getMajorOptions'])->name('major-options');
        Route::get('/export', [DataWargaSekolahController::class, 'export'])->name('export');
    });

    // ===== LOAN MANAGEMENT ROUTES (opsional) =====
    Route::prefix('dashboard/loans')->name('dashboard.loans.')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('index');
        Route::get('/create', [LoanController::class, 'create'])->name('create');
        Route::post('/', [LoanController::class, 'store'])->name('store');
        Route::get('/{id}', [LoanController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [LoanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LoanController::class, 'update'])->name('update');
        Route::delete('/{id}', [LoanController::class, 'destroy'])->name('destroy');
    });

    // ===== FIX CATEGORIES ROUTE =====
    Route::get('/fix-categories', function () {
        $firstCategory = \App\Models\Category::first();
        if (!$firstCategory) {
            return response()->json(['error' => 'No categories found'], 400);
        }
        
        $updated = \App\Models\Book::where(function($query) {
            $query->whereNull('category_id')->orWhere('category_id', 0);
        })->update(['category_id' => $firstCategory->category_id]);
        
        return response()->json([
            'message' => "Updated {$updated} books to category: {$firstCategory->category_name}",
            'updated_count' => $updated
        ]);
    })->name('fix.categories');

    // ===== LOGOUT ROUTE =====
    Route::post('/logout', function (Request $request) {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});