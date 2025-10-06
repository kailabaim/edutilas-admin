@extends('dashboard.layout')

@push('styles')
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
    .icon-wrapper.red { background: linear-gradient(135deg,#ef4444,#dc2626); }
    .icon-wrapper.orange { background: linear-gradient(135deg,#f59e0b,#d97706); }
    .stat-number { font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem; }
    .stat-label { color: #64748b; font-size: 0.875rem; }
    .chart-wrapper { height: 350px; padding: 1rem 0; }
    .toggle-group { display: flex; gap: 0.25rem; }
    .toggle-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; }
    .toggle-btn.active, .toggle-btn:hover { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-color: transparent; }
    .filter-card { 
        background: white; 
        border-radius: 16px; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
        overflow: hidden;
        border: 1px solid #e8ecf4;
    }
    
    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.75rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .filter-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .filter-title i {
        font-size: 1.25rem;
    }
    
    .filter-title h3 {
        margin: 0;
        font-size: 1.125rem;
        font-weight: 600;
    }
    
    .toggle-filter-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }
    
    .toggle-filter-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.05);
    }
    
    .toggle-filter-btn i {
        transition: transform 0.3s;
    }
    
    .toggle-filter-btn.collapsed i {
        transform: rotate(180deg);
    }
    
    .filter-content {
        padding: 2rem;
        transition: all 0.3s ease;
    }
    
    .filter-content.collapsed {
        display: none;
    }
    
    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .filter-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
    }
    
    .filter-label i {
        color: #667eea;
        font-size: 0.875rem;
    }
    
    .form-control, .select-custom {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.875rem;
        transition: all 0.2s;
        background: #f8fafc;
    }
    
    .form-control:focus, .select-custom:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 4px rgba(102,126,234,0.1);
    }
    
    .input-with-icon {
        position: relative;
    }
    
    .select-custom {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23667eea' d='M10.293 3.293L6 7.586 1.707 3.293A1 1 0 00.293 4.707l5 5a1 1 0 001.414 0l5-5a1 1 0 10-1.414-1.414z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
    }
    
    .filter-actions-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1.5rem;
        border-top: 2px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .filter-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #64748b;
    }
    
    .active-filters {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .active-filters i {
        color: #667eea;
    }
    
    .filter-badge {
        background: #e0e7ff;
        color: #667eea;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .filter-buttons {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .btn-reset, .btn-apply, .btn-export {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    
    .btn-reset {
        background: #f8fafc;
        color: #64748b;
        border: 2px solid #e2e8f0;
    }
    
    .btn-reset:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }
    
    .btn-apply {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-apply:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102,126,234,0.4);
    }
    
    .btn-export {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16,185,129,0.4);
    }
    
    @media (max-width: 768px) {
        .filter-row {
            grid-template-columns: 1fr;
        }
        
        .filter-actions-row {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-buttons {
            width: 100%;
            flex-direction: column;
        }
        
        .btn-reset, .btn-apply, .btn-export {
            width: 100%;
            justify-content: center;
        }
    }
    .book-cover { width: 40px; height: 50px; border-radius: 4px; overflow: hidden; flex-shrink: 0; }
    .book-img { width: 100%; height: 100%; object-fit: cover; }
    .book-placeholder { width: 100%; height: 100%; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1rem; }
    .transactions-scroll { max-height: 600px; overflow-y: auto; border: 1px solid #f1f5f9; border-radius: 8px; }
    .transactions-scroll::-webkit-scrollbar { width: 8px; }
    .transactions-scroll::-webkit-scrollbar-track { background: #f8fafc; }
    .transactions-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 1rem; border-bottom: 1px solid #f1f5f9; text-align: left; }
    .table th { background: #f8fafc; position: sticky; top: 0; z-index: 10; font-weight: 600; color: #374151; font-size: 0.875rem; }
    .d-flex { display: flex; }
    .align-items-center { align-items: center; }
    .gap-2 { gap: 0.5rem; }
    .gap-3 { gap: 0.75rem; }
    .fw-bold { font-weight: 600; color: #1e293b; }
    .text-muted { color: #64748b; }
    .text-primary { color: #6366f1; }
    .small { font-size: 0.85rem; }
    .text-center { text-align: center; }
    .py-4 { padding: 2rem 0; }
    .mb-2 { margin-bottom: 0.5rem; }
    .opacity-50 { opacity: 0.5; }
    .card-footer { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9; background: #fafbfc; }
    .pagination-info { font-size: 0.875rem; }
    .pagination-links { display: flex; gap: 0.5rem; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Manajemen Pengembalian Buku</h1>
    <div class="badge">
        <i class="fas fa-undo-alt"></i>
        <span>Data Pengembalian</span>
    </div>
</div>

<!-- Statistik Cards -->
<div class="cards">
    <div class="card">
        <div class="icon-wrapper blue">
            <i class="fas fa-undo"></i>
        </div>
        <div class="stat-number">{{ number_format($monthlyStats['total_returns']) }}</div>
        <div class="stat-label">Total Pengembalian {{ $monthlyStats['year'] }}</div>
    </div>

    <div class="card">
        <div class="icon-wrapper red">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-number">Rp {{ number_format($monthlyStats['total_fines'], 0, ',', '.') }}</div>
        <div class="stat-label">Total Denda</div>
    </div>

    <div class="card">
        <div class="icon-wrapper green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-number">{{ collect($monthlyStats['data'])->sum('good') }}</div>
        <div class="stat-label">Kondisi Baik</div>
    </div>

    <div class="card">
        <div class="icon-wrapper orange">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-number">{{ collect($monthlyStats['data'])->sum('damaged') + collect($monthlyStats['data'])->sum('lost') }}</div>
        <div class="stat-label">Rusak/Hilang</div>
    </div>
</div>

<!-- Chart Section -->
<div class="card">
    <div class="card-header">
        <h3>Statistik Pengembalian Bulanan {{ $monthlyStats['year'] }}</h3>
        <div class="toggle-group">
            <button class="toggle-btn active" id="btnMonthly">Bulanan</button>
            <button class="toggle-btn" id="btnYearly">Tahunan</button>
        </div>
    </div>
    <div class="chart-wrapper">
        <canvas id="returnChart"></canvas>
    </div>
</div>

<!-- Filter & Search Section -->
<div class="card filter-card">
    <div class="filter-header">
        <div class="filter-title">
            <i class="fas fa-filter"></i>
            <h3>Filter & Pencarian</h3>
        </div>
        <button type="button" class="toggle-filter-btn" id="toggleFilterBtn">
            <i class="fas fa-chevron-up"></i>
        </button>
    </div>
    
    <div class="filter-content" id="filterContent">
        <form method="GET" action="{{ route('dashboard.peminjaman') }}" class="filter-form">
            <div class="filter-row">
                <!-- Pencarian -->
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-search"></i>
                        Pencarian
                    </label>
                    <div class="input-with-icon">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               class="form-control"
                               placeholder="Cari nama, NIS/NIP, kode pengembalian...">
                    </div>
                </div>

                <!-- Kondisi Buku -->
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-bookmark"></i>
                        Kondisi Buku
                    </label>
                    <select name="status" class="form-control select-custom">
                        <option value="">Semua Kondisi</option>
                        <option value="good" {{ request('status') == 'good' ? 'selected' : '' }}>
                            ✓ Baik
                        </option>
                        <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>
                            ⚠ Rusak
                        </option>
                        <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>
                            ✗ Hilang
                        </option>
                    </select>
                </div>

                <!-- Tanggal Dari -->
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-calendar-alt"></i>
                        Tanggal Dari
                    </label>
                    <input type="date" 
                           name="date_from" 
                           value="{{ request('date_from') }}" 
                           class="form-control">
                </div>

                <!-- Tanggal Sampai -->
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-calendar-check"></i>
                        Tanggal Sampai
                    </label>
                    <input type="date" 
                           name="date_to" 
                           value="{{ request('date_to') }}" 
                           class="form-control">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="filter-actions-row">
                <div class="filter-info">
                    @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                        <span class="active-filters">
                            <i class="fas fa-info-circle"></i>
                            Filter aktif: 
                            @if(request('search'))
                                <span class="filter-badge">Pencarian</span>
                            @endif
                            @if(request('status'))
                                <span class="filter-badge">Kondisi</span>
                            @endif
                            @if(request('date_from') || request('date_to'))
                                <span class="filter-badge">Tanggal</span>
                            @endif
                        </span>
                    @endif
                </div>
                
                <div class="filter-buttons">
                    <a href="{{ route('dashboard.peminjaman') }}" class="btn-reset">
                        <i class="fas fa-times"></i>
                        Reset Filter
                    </a>
                    <button type="submit" class="btn-apply">
                        <i class="fas fa-search"></i>
                        Terapkan Filter
                    </button>
                    <a href="{{ route('dashboard.peminjaman.export', request()->query()) }}" class="btn-export">
                        <i class="fas fa-file-excel"></i>
                        Export Excel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Pengembalian -->
<div class="card">
    <div class="card-header">
        <h3>Data Pengembalian Buku ({{ number_format($returns->total()) }} Data)</h3>
    </div>

    <div class="transactions-scroll">
        <table class="table" id="returnsTable">
            <thead>
                <tr>
                    <th>Kode Pengembalian</th>
                    <th>Peminjam</th>
                    <th>Buku</th>
                    <th>Tanggal Kembali</th>
                    <th>Keterlambatan</th>
                    <th>Denda</th>
                    <th>Kondisi</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                    <tr>
                        <td>
                            <div class="fw-bold text-primary">{{ $return->return_code }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div>
                                    <div class="fw-bold">
                                        @if($return->student)
                                            {{ $return->student->full_name ?? 'N/A' }}
                                        @elseif($return->guru)
                                            {{ $return->guru->nama_guru ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        @if($return->student)
                                            NIS: {{ $return->student->nis ?? 'N/A' }}
                                            @if($return->kelas)
                                                <br>{{ $return->kelas->class_name ?? '' }}
                                            @endif
                                        @elseif($return->guru)
                                            NIP: {{ $return->guru->nip ?? 'N/A' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="book-cover">
                                    @if($return->book && $return->book->cover_url)
                                        <img src="{{ $return->book->cover_url }}" alt="Cover" class="book-img">
                                    @else
                                        <div class="book-placeholder">
                                            <i class="fas fa-book"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $return->book->title ?? 'N/A' }}</div>
                                    <div class="text-muted small">{{ $return->book->author ?? 'Penulis tidak diketahui' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">
                            {{ $return->return_date ? \Carbon\Carbon::parse($return->return_date)->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="text-center">
                            @if($return->days_late > 0)
                                <span class="badge" style="background: #fee2e2; color: #991b1b;">
                                    {{ $return->days_late }} hari
                                </span>
                            @else
                                <span class="badge" style="background: #dcfce7; color: #166534;">
                                    Tepat waktu
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold" style="color: #dc2626;">
                                Rp {{ number_format($return->fine_amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $conditionColor = match($return->book_condition) {
                                    'good' => ['#dcfce7', '#166534', 'Baik'],
                                    'damaged' => ['#fef3c7', '#92400e', 'Rusak'],
                                    'lost' => ['#fee2e2', '#991b1b', 'Hilang'],
                                    default => ['#f3f4f6', '#6b7280', 'N/A']
                                };
                            @endphp
                            <span class="badge" style="background: {{ $conditionColor[0] }}; color: {{ $conditionColor[1] }};">
                                {{ $conditionColor[2] }}
                            </span>
                        </td>
                        <td>
                            <div class="text-muted small">{{ $return->notes ?? '-' }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                            <div>Belum ada data pengembalian</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($returns->hasPages())
        <div class="card-footer">
            <div class="pagination-info">
                <span class="text-muted">
                    Menampilkan {{ $returns->firstItem() ?? 0 }} - {{ $returns->lastItem() ?? 0 }}
                    dari {{ $returns->total() ?? 0 }} data
                </span>
            </div>
            <div class="pagination-links">
                {{ $returns->links() }}
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Toggle filter collapse
    const toggleBtn = document.getElementById('toggleFilterBtn');
    const filterContent = document.getElementById('filterContent');
    
    if (toggleBtn && filterContent) {
        toggleBtn.addEventListener('click', function() {
            this.classList.toggle('collapsed');
            filterContent.classList.toggle('collapsed');
        });
    }

    const monthlyData = @json($monthlyStats['data']);
    
    const ctx = document.getElementById('returnChart');
    if (ctx) {
        let returnChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [{
                    label: 'Total Pengembalian',
                    data: monthlyData.map(d => d.total),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.2)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 3
                }, {
                    label: 'Kondisi Baik',
                    data: monthlyData.map(d => d.good),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.2)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 2
                }, {
                    label: 'Rusak',
                    data: monthlyData.map(d => d.damaged),
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.2)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 2
                }, {
                    label: 'Hilang',
                    data: monthlyData.map(d => d.lost),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.2)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            afterBody: function(context) {
                                const index = context[0].dataIndex;
                                const fines = monthlyData[index].fines;
                                const daysLate = monthlyData[index].days_late;
                                return `\nTotal Denda: Rp ${fines.toLocaleString('id-ID')}\nTotal Hari Terlambat: ${daysLate}`;
                            }
                        }
                    }
                }
            }
        });

        const btnMonthly = document.getElementById('btnMonthly');
        const btnYearly = document.getElementById('btnYearly');
        
        if (btnMonthly && btnYearly) {
            btnMonthly.addEventListener('click', function() {
                this.classList.add('active');
                btnYearly.classList.remove('active');
                returnChart.update();
            });

            btnYearly.addEventListener('click', function() {
                this.classList.add('active');
                btnMonthly.classList.remove('active');
                returnChart.update();
            });
        }
    }
</script>
@endpush