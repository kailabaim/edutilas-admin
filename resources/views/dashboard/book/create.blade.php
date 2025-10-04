@extends('dashboard.layout')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Tambah Buku Baru</h1>
        <div class="badge">
            <i class="fas fa-plus"></i>
            <span>Tambah Data Buku</span>
        </div>
    </div>

    <div class="card">
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('dashboard.buku') }}" style="padding: 0.5rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Buku
            </a>
        </div>

        <form method="POST" action="{{ route('dashboard.buku.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Left Column -->
                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Judul Buku *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;" placeholder="Masukkan judul buku">
                        @error('title')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Penulis *</label>
                        <input type="text" name="author" value="{{ old('author') }}" required style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;" placeholder="Masukkan nama penulis">
                        @error('author')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Kategori *</label>
                        <input type="text" name="category" value="{{ old('category') }}" required style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;" placeholder="Masukkan kategori buku" list="categories">
                        <datalist id="categories">
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </datalist>
                        @error('category')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Total Eksemplar *</label>
                            <input type="number" name="total_copies" value="{{ old('total_copies', 1) }}" required min="1" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                            @error('total_copies')
                                <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Tahun Terbit</label>
                            <input type="number" name="publication_year" value="{{ old('publication_year') }}" min="1900" max="{{ date('Y') }}" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                            @error('publication_year')
                                <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Deskripsi</label>
                        <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; resize: vertical;" placeholder="Masukkan deskripsi singkat tentang buku">{{ old('description') }}</textarea>
                        @error('description')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">URL Cover Buku</label>
                        <input type="url" name="cover_url" value="{{ old('cover_url') }}" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;" placeholder="https://example.com/cover.jpg">
                        @error('cover_url')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Upload Cover Buku</label>
                        <input type="file" name="cover_image" accept="image/*" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                        <div style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB</div>
                        @error('cover_image')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 8px; padding: 2rem; text-align: center; margin-bottom: 1.5rem;">
                        <i class="fas fa-image" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                        <div style="color: #6b7280; font-size: 0.875rem;">Preview cover akan muncul di sini</div>
                        <div id="preview-container" style="margin-top: 1rem; display: none;">
                            <img id="cover-preview" style="max-width: 200px; max-height: 300px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                        </div>
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem; display: flex; justify-content: end; gap: 1rem;">
                <button type="button" onclick="window.history.back()" style="padding: 0.75rem 1.5rem; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 8px; color: #374151; font-weight: 600; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-save"></i> Simpan Buku
                </button>
            </div>
        </form>
    </div>

    <script>
        // Preview cover image
        document.querySelector('input[name="cover_image"]').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('cover-preview').src = e.target.result;
                    document.getElementById('preview-container').style.display = 'block';
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        // Preview dari URL
        document.querySelector('input[name="cover_url"]').addEventListener('input', function(e) {
            if (e.target.value) {
                document.getElementById('cover-preview').src = e.target.value;
                document.getElementById('preview-container').style.display = 'block';
            }
        });
    </script>
@endsection