@extends('dashboard.layout')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard Admin</h1>
    <div class="badge">
        <i class="fas fa-user-shield"></i>
        <span>Panel Administrasi</span>
    </div>
</div>

<!-- Statistik Cards -->
<div class="cards">
    <div class="card">
        <div class="icon-wrapper blue">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-number">{{ number_format($totalBooks) }}</div>
        <div class="stat-label">Total Koleksi Buku</div>
    </div>

    <div class="card">
        <div class="icon-wrapper green">
            <i class="fas fa-hand-holding"></i>
        </div>
        <div class="stat-number">{{ number_format($activeBorrows) }}</div>
        <div class="stat-label">Peminjaman Aktif</div>
    </div>
</div>

<!-- Semua Transaksi Peminjaman -->
<div class="card">
    <div class="card-header">
        <h3>Semua Transaksi Peminjaman ({{ number_format($recentTransactions->count()) }} Data)</h3>
    </div>
    <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Cari Nama Peminjam.....">
    </div>

    <div class="transactions-scroll">
        <table class="table" id="transactionsTable">
            <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Status</th>
                    <th>Aksi Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $index => $transaction)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div>
                                    <div class="fw-bold">
                                        @if($transaction->student)
                                            {{ $transaction->student->full_name ?? 'N/A' }}
                                        @elseif($transaction->guru)
                                            {{ $transaction->guru->nama_guru ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        @if($transaction->student && $transaction->student->kelas)
                                            {{ $transaction->student->kelas->class_name ?? 'N/A' }}
                                            @if($transaction->student->kelas->major)
                                                - {{ $transaction->student->kelas->major }}
                                            @endif
                                        @elseif($transaction->guru)
                                            Guru
                                        @else
                                            NIS: {{ $transaction->student->nis ?? 'N/A' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="book-cover">
                                    @if($transaction->book && $transaction->book->cover_url)
                                        <img src="{{ $transaction->book->cover_url }}" alt="Cover" class="book-img">
                                    @else
                                        <div class="book-placeholder">
                                            <i class="fas fa-book"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $transaction->book->title ?? 'N/A' }}</div>
                                    <div class="text-muted small">{{ $transaction->book->author ?? 'Penulis tidak diketahui' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">
                            {{ $transaction->loan_date ? \Carbon\Carbon::parse($transaction->loan_date)->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td>
                            @php
                                $statusColor = match($transaction->status) {
                                    'active' => ['#fef3c7', '#92400e', 'Aktif'],
                                    'returned' => ['#dcfce7', '#166534', 'Dikembalikan'],
                                    'overdue' => ['#fee2e2', '#991b1b', 'Terlambat'],
                                    default => ['#f3f4f6', '#6b7280', 'Tidak Diketahui']
                                };
                            @endphp
                            <span class="badge" style="background: {{ $statusColor[0] }}; color: {{ $statusColor[1] }};">
                                {{ $statusColor[2] }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn-icon" title="Lihat Detail" onclick="showDetail({{ $loop->index }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                            <div>Belum ada transaksi peminjaman</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail Peminjaman -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Detail Transaksi Peminjaman</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-grid">
                <!-- Informasi Peminjam -->
                <div class="detail-section">
                    <h4><i class="fas fa-user"></i> Informasi Peminjam</h4>
                    <div class="detail-item">
                        <label>Nama Lengkap:</label>
                        <span id="student-name">-</span>
                    </div>
                    <div class="detail-item">
                        <label>NIS/NIP:</label>
                        <span id="student-nis">-</span>
                    </div>
                    <div class="detail-item">
                        <label>Kategori:</label>
                        <span id="student-class">-</span>
                    </div>
                </div>

                <!-- Informasi Buku -->
                <div class="detail-section">
                    <h4><i class="fas fa-book"></i> Informasi Buku</h4>
                    <div class="book-detail">
                        <div class="book-cover-large" id="book-cover-container">
                            <img id="book-cover-large" src="" alt="Cover Buku" style="display: none;">
                            <div id="book-placeholder-large" class="book-placeholder-large">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                        <div class="book-info">
                            <div class="detail-item">
                                <label>Judul Buku:</label>
                                <span id="book-title">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Penulis:</label>
                                <span id="book-author">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Kategori:</label>
                                <span id="book-category">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Peminjaman -->
                <div class="detail-section">
                    <h4><i class="fas fa-calendar"></i> Detail Peminjaman</h4>

                    <div class="detail-item">
                        <label>Tanggal Pinjam:</label>
                        <span id="loan-date">-</span>
                    </div>
                    <div class="detail-item">
                        <label>Batas Waktu:</label>
                        <span id="due-date">-</span>
                    </div>

                    <div class="detail-item" id="fine-section" style="display: none;">
                        <label>Denda:</label>
                        <span id="fine-amount" class="text-danger fw-bold">-</span>
                    </div>
                </div>

                <!-- Catatan atau Keterangan -->
                <div class="detail-section">
                    <h4><i class="fas fa-sticky-note"></i> Catatan</h4>
                    <div class="detail-item">
                        <label>Keterangan:</label>
                        <p id="transaction-notes" class="notes-text">-</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">Tutup</button>
            <button class="btn btn-primary" id="action-button" onclick="handleAction()">Aksi</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Store transaction data for modal
    const transactionsData = @json($recentTransactions->toArray());
    
    // Chart.js code (existing)
    const ctx = document.getElementById('loanChart');
    if (ctx) {
        const monthlyLabels = @json($months ?? []);
        const monthlyData   = @json($totalsMonthly ?? []);
        const yearlyLabels  = @json($years ?? []);
        const yearlyData    = @json($totalsYearly ?? []);

        let loanChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Peminjaman Bulanan',
                    data: monthlyData,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.2)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Toggle between monthly and yearly view
        const btnMonthly = document.getElementById('btnMonthly');
        const btnYearly = document.getElementById('btnYearly');
        
        if (btnMonthly && btnYearly) {
            btnMonthly.addEventListener('click', function() {
                this.classList.add('active');
                btnYearly.classList.remove('active');
                
                loanChart.data.labels = monthlyLabels;
                loanChart.data.datasets[0].data = monthlyData;
                loanChart.data.datasets[0].label = "Peminjaman Bulanan";
                loanChart.update();
            });

            btnYearly.addEventListener('click', function() {
                this.classList.add('active');
                btnMonthly.classList.remove('active');
                
                loanChart.data.labels = yearlyLabels;
                loanChart.data.datasets[0].data = yearlyData;
                loanChart.data.datasets[0].label = "Peminjaman Tahunan";
                loanChart.update();
            });
        }
    }

    // Modal functions - UPDATED WITH RETURN FUNCTIONALITY
    function showDetail(index) {
        console.log('showDetail called with index:', index);
        console.log('transactionsData:', transactionsData);
        
        if (!transactionsData || !transactionsData[index]) {
            console.error('Transaction data not found for index:', index);
            alert('Data transaksi tidak ditemukan!');
            return;
        }
        
        const transaction = transactionsData[index];
        console.log('Selected transaction:', transaction);

        // Populate student information - UPDATED untuk handle guru
        const studentName = getNestedValue(transaction, 'student.full_name') || getNestedValue(transaction, 'guru.nama_guru') || 'N/A';
        const studentNis = getNestedValue(transaction, 'student.nis') || getNestedValue(transaction, 'guru.nip') || 'N/A';
        
        document.getElementById('student-name').textContent = studentName;
        document.getElementById('student-nis').textContent = studentNis;
        
        // Handle class information - UPDATED untuk handle guru
        let className = 'N/A';
        const kelas = getNestedValue(transaction, 'student.kelas');
        if (kelas) {
            if (kelas.class_name) {
                className = kelas.class_name + (kelas.major ? ' - ' + kelas.major : '');
            } else if (kelas.nama_kelas) {
                className = kelas.nama_kelas;
            }
        } else if (getNestedValue(transaction, 'guru.nama_guru')) {
            className = 'Guru';
        }
        document.getElementById('student-class').textContent = className;

        // Populate book information
        const bookTitle = getNestedValue(transaction, 'book.title') || 'N/A';
        const bookAuthor = getNestedValue(transaction, 'book.author') || 'Penulis tidak diketahui';
        
        // Debug category data
        console.log('Book data:', transaction.book);
        console.log('Category relation:', getNestedValue(transaction, 'book.categoryRelation'));
        console.log('Category string:', getNestedValue(transaction, 'book.category'));
        
        const bookCategory = getNestedValue(transaction, 'book.categoryRelation.category_name') || 
                            getNestedValue(transaction, 'book.category') || 'Tidak Dikategorikan';        
        const bookCoverUrl = getNestedValue(transaction, 'book.cover_url');
        
        document.getElementById('book-title').textContent = bookTitle;
        document.getElementById('book-author').textContent = bookAuthor;
        document.getElementById('book-category').textContent = bookCategory;

        // Handle book cover
        const bookCover = document.getElementById('book-cover-large');
        const bookPlaceholder = document.getElementById('book-placeholder-large');
        
        if (bookCoverUrl) {
            bookCover.src = bookCoverUrl;
            bookCover.style.display = 'block';
            bookPlaceholder.style.display = 'none';
        } else {
            bookCover.style.display = 'none';
            bookPlaceholder.style.display = 'flex';
        }

        // Populate transaction information
        document.getElementById('loan-date').textContent = transaction.loan_date ? formatDate(transaction.loan_date) : 'N/A';
        document.getElementById('due-date').textContent = transaction.due_date ? formatDate(transaction.due_date) : 'N/A';


        // Handle fine
        const fineSection = document.getElementById('fine-section');
        const fineAmount = document.getElementById('fine-amount');
        if (transaction.fine && transaction.fine > 0) {
            fineSection.style.display = 'block';
            fineAmount.textContent = 'Rp ' + number_format(transaction.fine);
        } else {
            fineSection.style.display = 'none';
        }

        // Handle notes
        document.getElementById('transaction-notes').textContent = transaction.notes || 'Tidak ada catatan khusus';

        // Update action button based on status - UPDATED
        const actionButton = document.getElementById('action-button');
        if (transaction.status === 'active' || transaction.status === 'overdue') {
            actionButton.textContent = 'Kembalikan Buku';
            actionButton.className = 'btn btn-success';
            actionButton.disabled = false;
        } else {
            actionButton.textContent = 'Buku Dikembalikan';
            actionButton.className = 'btn btn-secondary';
            actionButton.disabled = true;
        }

        // Store transaction data for action handling - FIXED to use loan_id
        actionButton.setAttribute('data-transaction-id', transaction.loan_id);
        actionButton.setAttribute('data-transaction-status', transaction.status);

        // Show modal
        document.getElementById('detailModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('detailModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
function handleAction() {
    console.log('handleAction dipanggil - START');
    
    const actionButton = document.getElementById('action-button');
    console.log('actionButton:', actionButton);
    
    const transactionId = actionButton.getAttribute('data-transaction-id');
    console.log('transactionId dari button:', transactionId);
    
    // Tanyakan input kondisi buku & denda sebelum proses
    const conditionOptions = ['good', 'damaged', 'lost'];
    let bookCondition = prompt('Masukkan kondisi buku (good/damaged/lost):', 'good');
    if (!bookCondition) return; // dibatalkan
    bookCondition = bookCondition.toLowerCase().trim();
    if (!conditionOptions.includes(bookCondition)) {
        alert('Kondisi tidak valid. Gunakan salah satu: good, damaged, lost');
        return;
    }

    let fineAmountInput = prompt('Masukkan denda (angka, boleh kosong untuk otomatis):', '');
    let fineAmount = null;
    if (fineAmountInput !== null && fineAmountInput.trim() !== '') {
        const parsed = Number(fineAmountInput.replace(/\D/g, ''));
        if (isNaN(parsed)) {
            alert('Denda harus berupa angka.');
            return;
        }
        fineAmount = parsed;
    }

    let notes = prompt('Catatan pengembalian (opsional):', '');

    console.log('Akan memanggil processBookReturn...');
    processBookReturn(transactionId, actionButton, { book_condition: bookCondition, fine_amount: fineAmount, notes });
}
 function calculateAndShowFine(transactionId, callback) {
    console.log('calculateAndShowFine dipanggil dengan ID:', transactionId);
    
    // Debug: cek CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    console.log('CSRF Token element:', csrfToken);
    console.log('CSRF Token value:', csrfToken ? csrfToken.getAttribute('content') : 'TIDAK DITEMUKAN');
    
    // Hitung denda terlebih dahulu
    const url = `/dashboard/calculate-fine/${transactionId}`;
    console.log('URL yang akan dipanggil:', url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
        }
    })
    .then(response => {
        console.log('Response received:', response);
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Data from server:', data);
        
        if (data.success) {
            let confirmMessage = 'Apakah Anda yakin ingin menandai buku ini sudah dikembalikan?';
            
            if (data.fine > 0) {
                confirmMessage += `\n\nDenda keterlambatan: Rp ${number_format(data.fine)}`;
                confirmMessage += `\nJumlah hari terlambat: ${data.days_late} hari`;
            }
            
            console.log('Menampilkan konfirmasi:', confirmMessage);
            if (confirm(confirmMessage)) {
                console.log('User konfirmasi OK, memanggil callback');
                callback();
            } else {
                console.log('User membatalkan');
            }
        } else {
            console.error('Server error:', data.message);
            if (confirm('Apakah Anda yakin ingin menandai buku ini sudah dikembalikan?')) {
                console.log('User konfirmasi OK meski ada error, memanggil callback');
                callback();
            }
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        if (confirm('Apakah Anda yakin ingin menandai buku ini sudah dikembalikan?')) {
            console.log('User konfirmasi OK meski ada network error, memanggil callback');
            callback();
        }
    });
}

function processBookReturn(transactionId, actionButton, payload) {
    console.log('processBookReturn dipanggil dengan ID:', transactionId);
    
    // Show loading state
    actionButton.disabled = true;
    actionButton.textContent = 'Memproses...';

    const url = `/dashboard/return/${transactionId}`;
    console.log('URL untuk return:', url);
    
    // Debug: cek CSRF token lagi
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    console.log('CSRF Token saat return:', csrfToken ? csrfToken.getAttribute('content') : 'TIDAK DITEMUKAN');

    // Make AJAX call to backend
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
        },
        body: JSON.stringify(payload || {})
    })
    .then(response => {
        console.log('Response return:', response);
        console.log('Response status return:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Data return dari server:', data);
        
        if (data.success) {
            // Show success message with details
            let successMessage = 'Buku berhasil dikembalikan!';
            successMessage += `\n\nKode Return: ${data.data.return_code}`;
            successMessage += `\nJudul Buku: ${data.data.book_title}`;
            successMessage += `\nStok Tersedia: ${data.data.available_copies}`;
            
            if (data.data.fine_amount > 0) {
                successMessage += `\n\nDenda: Rp ${number_format(data.data.fine_amount)}`;
                successMessage += `\nHari Terlambat: ${data.data.days_late} hari`;
            }
            
            alert(successMessage);
            
            // Update button state
            actionButton.textContent = 'Data Dikembalikan';
            actionButton.className = 'btn btn-secondary';
            actionButton.disabled = true;
            
            // Refresh the page after 3 seconds
            setTimeout(() => {
                window.location.reload();
            }, 3000);
            
        } else {
            console.error('Server return error:', data.message);
            alert('Gagal mengembalikan buku: ' + (data.message || 'Terjadi kesalahan'));
            actionButton.disabled = false;
            actionButton.textContent = 'Tandai Dikembalikan';
        }
    })
    .catch(error => {
        console.error('Network return error:', error);
        alert('Terjadi kesalahan saat menghubungi server');
        actionButton.disabled = false;
        actionButton.textContent = 'Tandai Dikembalikan';
    });
}
    // Utility functions
    function getNestedValue(obj, path) {
        return path.split('.').reduce((current, key) => {
            return current && current[key] !== undefined ? current[key] : null;
        }, obj);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    function getStatusInfo(status) {
        switch(status) {
            case 'active':
                return { text: 'Aktif', bg: '#fef3c7', color: '#92400e' };
            case 'returned':
                return { text: 'Dikembalikan', bg: '#dcfce7', color: '#166534' };
            case 'overdue':
                return { text: 'Terlambat', bg: '#fee2e2', color: '#991b1b' };
            default:
                return { text: 'Tidak Diketahui', bg: '#f3f4f6', color: '#6b7280' };
        }
    }

    function number_format(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    // Close modal when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    // Search Filter
    document.getElementById("searchInput").addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll("#transactionsTable tbody tr");

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });

    // Debug: Log transaction data when page loads
    console.log('Page loaded. Transaction data:', transactionsData);
    
    // Debug: Check first transaction's book data
    if (transactionsData && transactionsData.length > 0) {
        console.log('First transaction book data:', transactionsData[0].book);
        console.log('First transaction book categoryRelation:', transactionsData[0].book?.categoryRelation);
    }
</script>

@endpush

<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .page-title { font-size: 1.875rem; font-weight: 700; color: #1e293b; margin: 0; }
    .badge { display: flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; }
    .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .card { padding: 1.5rem; border-radius: 12px; background: white; margin-bottom: 1.5rem; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
    .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
    .card-header h3 { margin: 0; font-size: 1.125rem; font-weight: 600; color: #1e293b; }
    .icon-wrapper { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; margin-bottom: 1rem; }
    .icon-wrapper.blue { background: linear-gradient(135deg,#3b82f6,#1d4ed8); }
    .icon-wrapper.green { background: linear-gradient(135deg,#10b981,#059669); }
    .stat-number { font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem; }
    .stat-label { color: #64748b; font-size: 0.875rem; }
    .toggle-group { display: flex; gap: 0.25rem; }
    .toggle-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; }
    .toggle-btn.active, .toggle-btn:hover { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-color: transparent; }
    .book-cover { width: 40px; height: 50px; border-radius: 4px; overflow: hidden; flex-shrink: 0; }
    .book-img { width: 100%; height: 100%; object-fit: cover; border-radius: 4px; }
    .book-placeholder { width: 100%; height: 100%; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1rem; border-radius: 4px; }
    .btn-primary { padding: 0.5rem 1rem; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(102,126,234,0.4); }
    .btn-icon { padding: 0.4rem 0.8rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; color: #64748b; cursor: pointer; font-size: 0.8rem; transition: all 0.2s; }
    .btn-icon:hover { background: #f1f5f9; transform: translateY(-1px); }
    
    /* Scrollable transactions table */
    .transactions-scroll {
        max-height: 600px;
        overflow-y: auto;
        border: 1px solid #f1f5f9;
        border-radius: 8px;
    }
    .transactions-scroll::-webkit-scrollbar {
        width: 8px;
    }
    .transactions-scroll::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 4px;
    }
    .transactions-scroll::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 4px;
    }
    .transactions-scroll::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
    
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 1rem; border-bottom: 1px solid #f1f5f9; text-align: left; }
    .table th { background: #f8fafc; position: sticky; top: 0; z-index: 10; font-weight: 600; color: #374151; font-size: 0.875rem; }
    .d-flex { display: flex; }
    .align-items-center { align-items: center; }
    .gap-2 { gap: 0.5rem; }
    .gap-3 { gap: 0.75rem; }
    .fw-bold { font-weight: 600; color: #1e293b; }
    .text-muted { color: #64748b; }
    .small { font-size: 0.85rem; }
    .badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .text-center { text-align: center; }
    .py-4 { padding: 2rem 0; }
    .mb-2 { margin-bottom: 0.5rem; }
    .opacity-50 { opacity: 0.5; }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1000;
        overflow-y: auto;
        padding: 20px;
    }

    .modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        max-width: 900px;
        margin: 50px auto;
        position: relative;
        animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        margin: 0;
        color: #1e293b;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #64748b;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: #f1f5f9;
        color: #374151;
    }

    .modal-body {
        padding: 2rem;
    }

    .detail-grid {
        display: grid;
        gap: 2rem;
    }

    .detail-section {
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 1.5rem;
        background: #fafbfc;
    }

    .detail-section h4 {
        margin: 0 0 1rem 0;
        color: #374151;
        font-size: 1.1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-section h4 i {
        color: #6366f1;
    }

    .detail-item {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 1rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-item label {
        font-weight: 600;
        color: #4b5563;
        font-size: 0.875rem;
    }

    .detail-item span {
        color: #1e293b;
    }

    .book-detail {
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
    }

    .book-cover-large {
        width: 120px;
        height: 150px;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .book-cover-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .book-placeholder-large {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 2rem;
    }

    .book-info {
        flex: 1;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .notes-text {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin: 0;
        font-style: italic;
        color: #64748b;
    }

    .modal-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-secondary {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #f1f5f9;
        color: #374151;
        transform: translateY(-1px);
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .btn-warning:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
    }

    .text-danger {
        color: #dc2626 !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .modal-content {
            margin: 20px auto;
            max-width: calc(100% - 40px);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
            flex-direction: column;
        }

        .detail-item {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .book-detail {
            flex-direction: column;
            text-align: center;
        }

        .book-cover-large {
            align-self: center;
        }
    }

    @media (max-width: 480px) {
        .modal {
            padding: 10px;
        }

        .modal-content {
            margin: 10px auto;
        }
    }
.search-bar {
    margin-bottom: 1rem;
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}

.search-bar input {
    width: 100%;
    padding: 0.7rem 1rem 0.7rem 2.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f9fafb;
    font-size: 0.9rem;
    color: #374151;
    transition: all 0.2s;
    outline: none;
}

.search-bar input:focus {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
}

.search-bar i {
    position: absolute;
    left: 12px;
    color: #9ca3af;
    font-size: 1rem;
}

</style>