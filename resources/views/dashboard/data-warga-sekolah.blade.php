@extends('dashboard.layout')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            Data Warga Sekolah
        </h1>
    </div>
    <div style="display: flex; gap: 1rem;">
        <button id="exportBtn" class="btn btn-secondary">
            <i class="fas fa-download"></i> Export Data
        </button>
        <button id="refreshBtn" class="btn btn-primary">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="cards" id="statsCards">
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: between;">
            <div>
                <h3 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;" id="totalStudents">
                    {{ number_format($stats['total_students']) }}
                </h3>
                <p style="color: #64748b; margin: 0;">Total Siswa</p>
            </div>
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-graduation-cap" style="color: white; font-size: 1.5rem;"></i>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: between;">
            <div>
                <h3 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;" id="totalTeachers">
                    {{ number_format($stats['total_teachers']) }}
                </h3>
                <p style="color: #64748b; margin: 0;">Total Guru</p>
            </div>
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f093fb, #f5576c); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chalkboard-teacher" style="color: white; font-size: 1.5rem;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Card -->
<div class="card" style="padding: 0; overflow: hidden;">
    <!-- Card Header -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 600; margin: 0; display: flex; align-items: center;">
            <i class="fas fa-users" style="margin-right: 0.75rem;"></i>
            Data Warga Sekolah
        </h2>
    </div>

    <!-- Card Body -->
    <div style="padding: 2rem;">
        <!-- Tabs -->
        <div style="display: flex; margin-bottom: 2rem; border-bottom: 2px solid #e2e8f0;">
            <button class="tab-button active" data-tab="students" style="padding: 1rem 1.5rem; background: none; border: none; font-size: 1rem; font-weight: 600; color: #718096; cursor: pointer; position: relative; transition: all 0.3s ease; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-graduation-cap"></i>
                Data Siswa
            </button>
            <button class="tab-button" data-tab="teachers" style="padding: 1rem 1.5rem; background: none; border: none; font-size: 1rem; font-weight: 600; color: #718096; cursor: pointer; position: relative; transition: all 0.3s ease; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chalkboard-teacher"></i>
                Data Guru
            </button>
        </div>

        <!-- Students Tab -->
        <div id="studentsTab" class="tab-content active">
            <div class="controls" style="display: flex; gap: 1.5rem; margin-bottom: 2rem; flex-wrap: wrap;">
                <div class="search-box" style="flex: 1; min-width: 300px; position: relative;">
                    <input type="text" id="searchStudents" placeholder="Cari nama siswa atau NIS..." style="width: 100%; padding: 0.75rem 3rem 0.75rem 1rem; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; transition: all 0.3s ease;">
                    <i class="fas fa-search" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #a0aec0;"></i>
                </div>
                <select id="filterClass" style="padding: 0.75rem 1rem; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; background: white; cursor: pointer; transition: all 0.3s ease;">
                    <option value="">Semua Kelas</option>
                    <option value="10">Kelas 10</option>
                    <option value="11">Kelas 11</option>
                    <option value="12">Kelas 12</option>
                    <option value="13">Kelas 13</option>
                </select>
                <select id="filterMajor" style="padding: 0.75rem 1rem; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; background: white; cursor: pointer; transition: all 0.3s ease;">
                    <option value="">Semua Jurusan</option>
                    <option value="KA">Kimia Analis</option>
                    <option value="RPL">Rekayasa Perangkat Lunak</option>
                    <option value="TKJ">Teknik Komputer dan Jaringan</option>
                </select>
            </div>

            <div style="overflow-x: auto;">
                <table class="data-table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <thead>
                        <tr style="background: #f7fafc;">
                            <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 600; color: #4a5568; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">No</th>
                            <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 600; color: #4a5568; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">NIS</th>
                            <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 600; color: #4a5568; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">Nama Lengkap</th>
                            <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 600; color: #4a5568; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">Kelas</th>
                            <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 600; color: #4a5568; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">Jurusan</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <!-- Data akan dimuat dengan JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Teachers Tab -->
        <div id="teachersTab" class="tab-content" style="display: none;">
            <div class="controls" style="display: flex; gap: 1.5rem; margin-bottom: 2rem; flex-wrap: wrap;">
                <div class="search-box" style="flex: 1; min-width: 300px; position: relative;">
                    <input type="text" id="searchTeachers" placeholder="Cari nama guru atau NIP..." style="width: 100%; padding: 0.75rem 3rem 0.75rem 1rem; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; transition: all 0.3s ease;">
                    <i class="fas fa-search" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #a0aec0;"></i>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="data-table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <thead>
                        <tr style="background: #f7fafc;">
                            <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 600; color: #4a5568; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">No</th>
                            <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 600; color: #4a5568; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">NIP</th>
                            <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 600; color: #4a5568; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">Nama Guru</th>
                        </tr>
                    </thead>
                    <tbody id="teachersTableBody">
                        <!-- Data akan dimuat dengan JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Tab Styles */
.tab-button.active {
    color: #667eea !important;
}

.tab-button.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: #667eea;
}

.tab-button:hover {
    color: #667eea;
}

/* Table Styles */
.data-table td {
    padding: 1rem 0.75rem;
    border-bottom: 1px solid #e2e8f0;
    color: #2d3748;
    font-size: 0.9rem;
}

.data-table tr:hover {
    background: #f7fafc;
}

/* Badge Styles */
.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge.active {
    background: #c6f6d5;
    color: #22543d;
}

.badge.inactive {
    background: #fed7d7;
    color: #742a2a;
}

/* Input Focus Styles */
input:focus, select:focus {
    outline: none;
    border-color: #667eea !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
}

/* Button Styles */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
    background: #f8fafc;
    color: #4a5568;
    border: 1px solid #e2e8f0;
}

.btn-secondary:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
}

/* Loading State */
.loading {
    text-align: center;
    padding: 2rem;
    color: #64748b;
}

.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Remove active class from all tabs
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.style.display = 'none';
            });
            
            // Add active class to clicked tab
            this.classList.add('active');
            document.getElementById(tabName + 'Tab').style.display = 'block';
            document.getElementById(tabName + 'Tab').classList.add('active');
            
            // Load data for active tab
            if (tabName === 'students') {
                loadStudents();
            } else if (tabName === 'teachers') {
                loadTeachers();
            }
        });
    });
    
    // Search functionality
    document.getElementById('searchStudents').addEventListener('input', debounce(loadStudents, 300));
    document.getElementById('searchTeachers').addEventListener('input', debounce(loadTeachers, 300));
    document.getElementById('filterClass').addEventListener('change', loadStudents);
    document.getElementById('filterMajor').addEventListener('change', loadStudents);
    
    // Refresh button
    document.getElementById('refreshBtn').addEventListener('click', function() {
        loadStats();
        const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
        if (activeTab === 'students') {
            loadStudents();
        } else {
            loadTeachers();
        }
        showToast('Berhasil', 'Data telah diperbarui', 'success');
    });
    
    // Export button
    document.getElementById('exportBtn').addEventListener('click', function() {
        const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
        exportData(activeTab);
    });
    
    // Load initial data
    loadStats();
    loadStudents();
});

// Load statistics
function loadStats() {
    fetch('/dashboard/api/data-warga-sekolah/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalStudents').textContent = new Intl.NumberFormat('id-ID').format(data.data.total_students);
                document.getElementById('totalTeachers').textContent = new Intl.NumberFormat('id-ID').format(data.data.total_teachers);
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
            showToast('Error', 'Gagal memuat statistik', 'error');
        });
}

// Load students data
function loadStudents() {
    const search = document.getElementById('searchStudents').value;
    const filterClass = document.getElementById('filterClass').value;
    const filterMajor = document.getElementById('filterMajor').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (filterClass) params.append('filterClass', filterClass);
    if (filterMajor) params.append('filterMajor', filterMajor);
    
    const tbody = document.getElementById('studentsTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="loading"><i class="fas fa-spinner"></i> Memuat data...</td></tr>';
    
    fetch('/dashboard/api/data-warga-sekolah/students?' + params.toString())
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderStudentsTable(data.data);
            } else {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: #ef4444;">Gagal memuat data siswa</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: #ef4444;">Terjadi kesalahan saat memuat data</td></tr>';
        });
}

// Load teachers data
function loadTeachers() {
    const search = document.getElementById('searchTeachers').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    
    const tbody = document.getElementById('teachersTableBody');
    tbody.innerHTML = '<tr><td colspan="4" class="loading"><i class="fas fa-spinner"></i> Memuat data...</td></tr>';
    
    fetch('/dashboard/api/data-warga-sekolah/teachers?' + params.toString())
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTeachersTable(data.data);
            } else {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 2rem; color: #ef4444;">Gagal memuat data guru</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading teachers:', error);
            tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 2rem; color: #ef4444;">Terjadi kesalahan saat memuat data</td></tr>';
        });
}

// Render students table
function renderStudentsTable(students) {
    const tbody = document.getElementById('studentsTableBody');
    
    if (students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">Tidak ada data siswa yang ditemukan</td></tr>';
        return;
    }
    
    tbody.innerHTML = students.map(student => `
        <tr>
            <td>${student.no}</td>
            <td>${student.nis}</td>
            <td>${student.nama_lengkap}</td>
            <td>${student.kelas}</td>
            <td>${student.jurusan}</td>
        </tr>
    `).join('');
}

// Render teachers table
function renderTeachersTable(teachers) {
    const tbody = document.getElementById('teachersTableBody');
    
    if (teachers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 2rem; color: #64748b;">Tidak ada data guru yang ditemukan</td></tr>';
        return;
    }
    
    tbody.innerHTML = teachers.map(teacher => `
        <tr>
            <td>${teacher.no}</td>
            <td>${teacher.nip}</td>
            <td>${teacher.nama_guru}</td>
        </tr>
    `).join('');
}

// Export data
function exportData(type) {
    const params = new URLSearchParams();
    params.append('type', type);
    params.append('format', 'csv');
    
    if (type === 'students') {
        const search = document.getElementById('searchStudents').value;
        const filterClass = document.getElementById('filterClass').value;
        const filterMajor = document.getElementById('filterMajor').value;
        
        if (search) params.append('search', search);
        if (filterClass) params.append('filterClass', filterClass);
        if (filterMajor) params.append('filterMajor', filterMajor);
    } else {
        const search = document.getElementById('searchTeachers').value;
        if (search) params.append('search', search);
    }
    
    window.open('/dashboard/api/data-warga-sekolah/export?' + params.toString(), '_blank');
    showToast('Export', 'Data sedang diunduh...', 'info');
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush

@endsection