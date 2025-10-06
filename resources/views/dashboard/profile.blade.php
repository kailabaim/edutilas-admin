@extends('dashboard.layout')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Profil Admin</h1>
            <p class="text-muted mb-0">Kelola informasi profil dan keamanan akun Anda</p>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Profil -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}
                        </div>
                    </div>
                    <h5 class="mb-1">{{ Auth::guard('admin')->user()->name }}</h5>
                    <p class="text-muted mb-2">{{ Auth::guard('admin')->user()->username }}</p>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-envelope me-1"></i>
                        {{ Auth::guard('admin')->user()->email }}
                    </p>
                    <hr>
                    <div class="text-start">
                        <p class="mb-2 small">
                            <strong>Bergabung Sejak:</strong><br>
                            <i class="fas fa-calendar-alt me-2 text-muted"></i>
                            {{ Auth::guard('admin')->user()->created_at->format('d F Y') }}
                        </p>
                        <p class="mb-0 small">
                            <strong>Terakhir Update:</strong><br>
                            <i class="fas fa-clock me-2 text-muted"></i>
                            {{ Auth::guard('admin')->user()->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tombol Logout -->
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="mb-3">Aksi Akun</h6>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-block w-100">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Form Edit Profil & Reset Password -->
        <div class="col-xl-8 col-lg-7">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Edit Profil -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-edit me-2"></i>Edit Profil
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   value="{{ old('name', Auth::guard('admin')->user()->name) }}" 
                                   required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" 
                                   value="{{ old('username', Auth::guard('admin')->user()->username) }}" 
                                   required>
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" 
                                   value="{{ old('email', Auth::guard('admin')->user()->email) }}" 
                                   required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reset Password -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key me-2"></i>Ubah Password
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            <small class="text-muted">Minimal 8 karakter</small>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-lock me-2"></i>Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    border-radius: 10px 10px 0 0 !important;
}

.btn-block {
    display: block;
    width: 100%;
}

.rounded-circle {
    font-weight: 600;
}
</style>
@endsection