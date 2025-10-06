@extends('dashboard.layout')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit Buku</h1>
        <div class="badge">
            <i class="fas fa-edit"></i>
            <span>Edit Data Buku</span>
        </div>
    </div>

    <div class="card">
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('dashboard.buku') }}" style="padding: 0.5rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Buku
            </a>
        </div>

        <form method="POST" action="{{ route('dashboard.buku.update', $book->book_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Left Column -->
                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Judul Buku *</label>
                        <input type="text" name="title" value="{{ old('title', $book->title) }}" required style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;" placeholder="Masukkan judul buku">
                        @error('title')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Penulis *</label>
                        <input type="text" name="author" value="{{ old('author', $book->author) }}" required style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;" placeholder="Masukkan nama penulis">
                        @error('author')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Kategori *</label>
                        <input type="text" name="category" value="{{ old('category', $book->category) }}" required style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;" placeholder="Masukkan kategori buku" list="categories">
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
                            <input type="number" name="total_copies" value="{{ old('total_copies', $book->total_copies ?: $book->stock) }}" required min="1" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                            @error('total_copies')
                                <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Tahun Terbit</label>
                            <input type="number" name="publication_year" value="{{ old('publication_year', $book->publication_year) }}" min="1900" max="{{ date('Y') }}" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                            @error('publication_year')
                                <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Status *</label>
                        <select name="status" required style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                            <option value="available" {{ old('status', $book->status) == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="borrowed" {{ old('status', $book->status) == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                            <option value="damaged" {{ old('status', $book->status) == 'damaged' ? 'selected' : '' }}>Rusak</option>
                            <option value="lost" {{ old('status', $book->status) == 'lost' ? 'selected' : '' }}>Hilang</option>
                        </select>
                        @error('status')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Deskripsi</label>
                        <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; resize: vertical;" placeholder="Masukkan deskripsi singkat tentang buku">{{ old('description', $book->description) }}</textarea>
                        @error('description')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">URL Cover Buku</label>
                        <input type="url" name="cover_url" value="{{ old('cover_url', $book->cover_url) }}" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;" placeholder="https://example.com/cover.jpg">
                        @error('cover_url')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Upload Cover Buku Baru</label>
                        <input type="file" name="cover_image" accept="image/*" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                        <div style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB</div>
                        @error('cover_image')
                            <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Current Cover Preview -->
                    <div style="background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 8px; padding: 2rem; text-align: center; margin-bottom: 1.5rem;">
                        @php
                            $currentCover = $book->cover_image ?: $book->cover_url;
                        @endphp
                        
                        @if($currentCover)
                            @php
                                $src = $currentCover;
                                if (!str_starts_with($src, ['http://', 'https://'])) {
                                    $src = asset('storage/' . $src);
                                }
                            @endphp
                            <div style="margin-bottom: 1rem;">
                                <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Cover Saat Ini:</div>
                                <img src="{{ $src }}" id="current-cover" style="max-width: 200px; max-height: 300px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div style="display: none; color: #dc2626; font-size: 0.875rem;">Gagal memuat gambar</div>
                            </div>
                        @else
                            <i class="fas fa-image" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                            <div style="color: #6b7280; font-size: 0.875rem;">Belum ada cover</div>
                        @endif
                        
                        <div id="preview-container" style="margin-top: 1rem; display: none;">
                            <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Preview Cover Baru:</div>
                            <img id="cover-preview" style="max-width: 200px; max-height: 300px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                        </div>
                    </div>

                    <!-- Book Statistics -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem;">
                        <h4 style="color: #374151; font-weight: 600; margin: 0 0 1rem 0;">Statistik Buku</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <div style="color: #6b7280; font-size: 0.875rem;">Tersedia</div>
                                <div style="font-weight: 700; color: #059669;">{{ $book->available_copies ?: $book->available_stock }} eksemplar</div>
                            </div>
                            <div>
                                <div style="color: #6b7280; font-size: 0.875rem;">Total Rating</div>
                                <div style="font-weight: 700; color: #dc2626;">{{ $book->total_ratings }} rating</div>
                            </div>
                            <div>
                                <div style="color: #6b7280; font-size: 0.875rem;">Rata-rata</div>
                                <div style="font-weight: 700; color: #f59e0b;">{{ number_format($book->average_rating, 1) }}/5.0</div>
                            </div>
                            <div>
                                <div style="color: #6b7280; font-size: 0.875rem;">Status</div>
                                <div style="font-weight: 700; color: #3b82f6;">{{ $book->getStatusLabel() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem; display: flex; justify-content: end; gap: 1rem;">
                <button type="button" onclick="window.history.back()" style="padding: 0.75rem 1.5rem; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 8px; color: #374151; font-weight: 600; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-save"></i> Update Buku
                </button>
            </div>
        </form>
    </div>

    <script>
        // Preview new cover image
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

        // Preview dari URL baru
        document.querySelector('input[name="cover_url"]').addEventListener('input', function(e) {
            if (e.target.value && e.target.value !== '{{ $book->cover_url }}') {
                document.getElementById('cover-preview').src = e.target.value;
                document.getElementById('preview-container').style.display = 'block';
            }
        });
    </script>
@endsection