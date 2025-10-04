@extends('dashboard.layout')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Manajemen Data Buku</h1>
        <div class="badge">
            <i class="fas fa-book"></i>
            <span>Koleksi Buku</span>
        </div>
    </div>

    <!-- Admin Controls -->
    <div class="card" style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Kontrol Admin</h3>
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('dashboard.buku.create') }}" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: .5rem;">
                    <i class="fas fa-plus"></i> Tambah Buku
                </a>
            </div>
        </div>
        
        <form method="GET" action="{{ route('dashboard.buku') }}" style="display: flex; gap: 1rem; align-items: center;">
            <div style="flex: 1; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari buku berdasarkan judul, penulis, atau deskripsi..." style="width: 100%; height: 45px; border: 2px solid #e2e8f0; border-radius: 12px; padding: 0 1rem 0 3rem; font-size: 1rem; outline: none; transition: all 0.3s ease;" onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 4px rgba(102, 126, 234, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
            </div>
            <button type="submit" style="height: 45px; padding: 0 1.5rem; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-search"></i> Cari
            </button>
        </form>
    </div>

    <!-- Books Grid -->
    <div class="cards">
        @forelse($books as $book)
            <div class="card" style="position: relative;">
                
                <div style="background: #f8fafc; height: 180px; border-radius: 12px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                    @php
                        $coverImage = $book->cover_image ?: $book->cover_url;
                    @endphp
                    
                    @if($coverImage)
                        @php
                            $src = $coverImage;
                            if (!\Illuminate\Support\Str::startsWith($src, ['http://', 'https://'])) {
                                $src = asset($src);
                            }
                        @endphp
                        <img src="{{ $src }}" alt="{{ $book->title }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div style="display: none; text-align: center; color: #64748b;">
                            <i class="fas fa-book" style="font-size: 3rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                            <div style="font-size: 0.9rem; font-weight: 600;">Cover Buku</div>
                        </div>
                    @else
                        <div style="text-align: center; color: #64748b;">
                            <i class="fas fa-book" style="font-size: 3rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                            <div style="font-size: 0.9rem; font-weight: 600;">Cover Buku</div>
                        </div>
                    @endif
                    
                    <!-- Status Badge -->
                    <div style="position: absolute; top: 0.5rem; left: 0.5rem;">
                        @php
                            $statusColors = [
                                'available' => ['#dcfce7', '#166534', 'Tersedia'],
                                'borrowed' => ['#fef3c7', '#92400e', 'Dipinjam'],
                                'damaged' => ['#fee2e2', '#991b1b', 'Rusak'],
                                'lost' => ['#fee2e2', '#991b1b', 'Hilang']
                            ];
                            $statusColor = $statusColors[$book->status] ?? ['#f3f4f6', '#6b7280', 'Tidak Diketahui'];
                        @endphp
                        <span style="background: {{ $statusColor[0] }}; color: {{ $statusColor[1] }}; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">{{ $statusColor[2] }}</span>
                    </div>
                    
                    @if($book->average_rating > 0)
                        <div style="position: absolute; top: 0.5rem; right: 0.5rem;">
                            <span style="background: rgba(255, 255, 255, 0.9); color: #f59e0b; padding: 0.25rem 0.5rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                                <i class="fas fa-star"></i> {{ number_format($book->average_rating, 1) }}
                            </span>
                        </div>
                    @endif
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <h4 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $book->title }}</h4>
                    <p style="color: #64748b; font-size: 0.9rem; margin: 0 0 0.5rem 0;">Penulis: {{ $book->author }}</p>
                    <p style="color: #64748b; font-size: 0.85rem; margin: 0 0 0.25rem 0;">Kategori: {{ $book->categoryRelation ? $book->categoryRelation->category_name : ($book->category ?? 'Tidak Dikategorikan') }}</p>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                    <div>
                        <div style="font-size: 0.85rem; color: #64748b;">Tersedia</div>
                        <div style="font-weight: 700; color: #1e293b;">
                            {{ $book->available_copies ?? $book->available_stock ?? 0 }} / {{ $book->total_copies ?? $book->stock ?? 0 }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; color: #64748b;">Status</div>
                        <div style="font-weight: 700; color: {{ $statusColor[1] }};">{{ $statusColor[2] }}</div>
                    </div>
                    <button onclick="viewBookDetail({{ $book->book_id }})" style="padding: 0.5rem 1rem; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-eye"></i> Detail
                    </button>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; color: #64748b;">
                <i class="fas fa-book" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <div style="font-size: 1.2rem; font-weight: 600; margin-bottom: 0.5rem;">Tidak ada buku ditemukan</div>
                <div>Silakan coba dengan kata kunci atau filter yang berbeda</div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($books->hasPages())
        <div style="display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 2rem;">
            @if ($books->onFirstPage())
                <span style="padding: 0.75rem 1rem; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; color: #9ca3af; cursor: not-allowed;">
                    <i class="fas fa-chevron-left"></i> Sebelumnya
                </span>
            @else
                <a href="{{ $books->previousPageUrl() }}" style="padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #64748b; text-decoration: none; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                    <i class="fas fa-chevron-left"></i> Sebelumnya
                </a>
            @endif

            <div style="display: flex; gap: 0.5rem;">
                @foreach ($books->getUrlRange(1, $books->lastPage()) as $page => $url)
                    @if ($page == $books->currentPage())
                        <span style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 8px; font-weight: 600; display: flex; align-items: center; justify-content: center;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="width: 40px; height: 40px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #64748b; font-weight: 600; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">{{ $page }}</a>
                    @endif
                @endforeach
            </div>

            @if ($books->hasMorePages())
                <a href="{{ $books->nextPageUrl() }}" style="padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #64748b; text-decoration: none; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                    Selanjutnya <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span style="padding: 0.75rem 1rem; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; color: #9ca3af; cursor: not-allowed;">
                    Selanjutnya <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    @endif

    <!-- Modal Detail Buku -->
    <div id="bookDetailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; border-radius: 20px; width: 90%; max-width: 900px; max-height: 90vh; overflow-y: auto; position: relative;">
            <div style="padding: 2rem 2rem 0 2rem; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0;">Detail Buku</h2>
                    <button onclick="closeBookDetailModal()" style="width: 40px; height: 40px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 50%; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div id="modalContent" style="padding: 2rem;"></div>
        </div>
    </div>

    <!-- Modal Edit Buku -->
    <div id="editBookModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1001; justify-content: center; align-items: center;">
        <div style="background: white; border-radius: 20px; width: 90%; max-width: 700px; max-height: 90vh; overflow-y: auto; position: relative;">
            <div style="padding: 2rem 2rem 0 2rem; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0;">Edit Buku</h2>
                    <button onclick="closeEditModal()" style="width: 40px; height: 40px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 50%; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div id="editModalContent" style="padding: 2rem;"></div>
        </div>
    </div>
    
    <script>
        let currentBookData = null;

        // Get CSRF token dari meta tag
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        }

        // Function untuk menampilkan modal detail buku
        function viewBookDetail(bookId) {
            const modal = document.getElementById('bookDetailModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.style.display = 'flex';
            modalContent.innerHTML = '<div style="text-align: center; padding: 2rem;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #667eea;"></i><br><br>Memuat data...</div>';
            
            fetch(`/dashboard/buku/${bookId}/detail`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentBookData = data.book;
                        modalContent.innerHTML = generateBookDetailHTML(data.book);
                    } else {
                        modalContent.innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc2626;">Gagal memuat data buku.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalContent.innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc2626;">Terjadi kesalahan saat memuat data.</div>';
                });
        }

        // Function untuk menutup modal detail
        function closeBookDetailModal() {
            document.getElementById('bookDetailModal').style.display = 'none';
            currentBookData = null;
        }

        // Function untuk generate HTML detail buku
        function generateBookDetailHTML(book) {
            const statusColors = {
                'available': ['#dcfce7', '#166534', 'Tersedia'],
                'borrowed': ['#fef3c7', '#92400e', 'Dipinjam'],
                'damaged': ['#fee2e2', '#991b1b', 'Rusak'],
                'lost': ['#fee2e2', '#991b1b', 'Hilang']
            };
            
            const statusColor = statusColors[book.status] || ['#f3f4f6', '#6b7280', 'Tidak Diketahui'];
            const coverImage = book.cover_image || book.cover_url;
            let coverHTML;

            if (coverImage) {
                let src = coverImage;
                if (!src.startsWith('http://') && !src.startsWith('https://')) {
                    src = `{{ asset('') }}${src}`;
                }
                coverHTML = `<img src="${src}" alt="${book.title}" style="width: 100%; height: 300px; object-fit: cover; border-radius: 12px;">`;
            } else {
                coverHTML = `
                    <div style="background: #f8fafc; height: 300px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #64748b;">
                        <div style="text-align: center;">
                            <i class="fas fa-book" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                            <div style="font-size: 1rem; font-weight: 600;">Cover Buku</div>
                        </div>
                    </div>
                `;
            }

            return `
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">
                    <div>
                        ${coverHTML}
                        <div style="margin-top: 1rem; text-align: center;">
                            <span style="background: ${statusColor[0]}; color: ${statusColor[1]}; padding: 0.5rem 1rem; border-radius: 25px; font-weight: 600;">${statusColor[2]}</span>
                        </div>
                        ${book.average_rating > 0 ? `
                        <div style="margin-top: 1rem; text-align: center;">
                            <div style="font-size: 2rem; color: #f59e0b; margin-bottom: 0.5rem;">
                                ${'★'.repeat(Math.floor(book.average_rating))}${'☆'.repeat(5 - Math.floor(book.average_rating))}
                            </div>
                            <div style="font-size: 1.1rem; font-weight: 600; color: #1e293b;">${parseFloat(book.average_rating).toFixed(1)}/5</div>
                            <div style="font-size: 0.9rem; color: #64748b;">${book.total_ratings} rating</div>
                        </div>
                        ` : ''}
                    </div>
                    
                    <div>
                        <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin: 0 0 1rem 0; line-height: 1.3;">${book.title}</h1>
                        
                        <div style="display: grid; gap: 1rem;">
                            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 1rem; align-items: start;">
                                <span style="font-weight: 600; color: #64748b;">Penulis:</span>
                                <span style="color: #1e293b;">${book.author}</span>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 1rem; align-items: start;">
                                <span style="font-weight: 600; color: #64748b;">Kategori:</span>
                                <span style="color: #1e293b;">${book.category || 'Tidak Dikategorikan'}</span>
                            </div>
                        
                            
                            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 1rem; align-items: start;">
                                <span style="font-weight: 600; color: #64748b;">Total Buku:</span>
                                <span style="color: #1e293b;">${book.total_copies || 0} buku</span>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 1rem; align-items: start;">
                                <span style="font-weight: 600; color: #64748b;">Tersedia:</span>
                                <span style="color: #1e293b;">${book.available_copies || 0} buku</span>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 1rem; align-items: start;">
                                <span style="font-weight: 600; color: #64748b;">Status:</span>
                                <span style="color: ${statusColor[1]}; font-weight: 600;">${statusColor[2]}</span>
                            </div>
                        </div>
                        
                        ${book.description ? `
                        <div style="margin-top: 2rem;">
                            <h3 style="font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0 0 0.5rem 0;">Deskripsi</h3>
                            <p style="color: #64748b; line-height: 1.6; margin: 0;">${book.description}</p>
                        </div>
                        ` : ''}
                        
                        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                            <button onclick="openEditModal(${book.book_id})" style="flex: 1; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                <i class="fas fa-edit"></i> Edit Buku
                            </button>
                            <button onclick="deleteBook(${book.book_id})" style="flex: 1; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #dc2626, #991b1b); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                <i class="fas fa-trash"></i> Hapus Buku
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        // Function untuk membuka modal edit
        function openEditModal(bookId) {
            if (!currentBookData) return;
            
            const modal = document.getElementById('editBookModal');
            const modalContent = document.getElementById('editModalContent');
            
            modal.style.display = 'flex';
            modalContent.innerHTML = generateEditFormHTML(currentBookData);
        }

        // Function untuk menutup modal edit
        function closeEditModal() {
            document.getElementById('editBookModal').style.display = 'none';
        }

        // Function untuk generate form edit
        function generateEditFormHTML(book) {
            return `
                <form id="editBookForm" onsubmit="submitEditForm(event, ${book.book_id})" style="display: grid; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Judul Buku *</label>
                        <input type="text" name="title" value="${book.title}" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem;">
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Penulis *</label>
                        <input type="text" name="author" value="${book.author}" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem;">
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Kategori *</label>
                        <input type="text" name="category" value="${book.category || ''}" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem;">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Total Buku *</label>
                            <input type="number" name="total_copies" value="${book.total_copies || 0}" required min="1" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem;">
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Status *</label>
                        <select name="status" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem;">
                            <option value="available" ${book.status === 'available' ? 'selected' : ''}>Tersedia</option>
                            <option value="borrowed" ${book.status === 'borrowed' ? 'selected' : ''}>Dipinjam</option>
                            <option value="damaged" ${book.status === 'damaged' ? 'selected' : ''}>Rusak</option>
                            <option value="lost" ${book.status === 'lost' ? 'selected' : ''}>Hilang</option>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Cover URL</label>
                        <input type="url" name="cover_url" value="${book.cover_url || ''}" placeholder="https://example.com/cover.jpg" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem;">
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Deskripsi</label>
                        <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; resize: vertical;">${book.description || ''}</textarea>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                        <button type="button" onclick="closeEditModal()" style="flex: 1; padding: 0.75rem 1.5rem; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px; font-weight: 600; cursor: pointer; color: #64748b;">
                            Batal
                        </button>
                        <button type="submit" style="flex: 1; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer;">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            `;
        }

        // Function untuk submit form edit - SUDAH DIPERBAIKI
        function submitEditForm(event, bookId) {
            event.preventDefault();
            
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Buat FormData dari form
            const formData = new FormData(form);
            
            // Tambahkan method spoofing untuk Laravel
            formData.append('_method', 'PUT');
            
            // Kirim request dengan FormData (bukan JSON)
            fetch(`/dashboard/buku/${bookId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    // Tampilkan toast notification
                    if (window.showToast) {
                        window.showToast('Berhasil!', 'Buku berhasil diperbarui', 'success');
                    } else {
                        alert('Buku berhasil diperbarui!');
                    }
                    
                    closeEditModal();
                    closeBookDetailModal();
                    
                    // Reload halaman setelah delay singkat
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    if (window.showToast) {
                        window.showToast('Gagal!', data.message || 'Gagal memperbarui buku', 'error');
                    } else {
                        alert('Error: ' + (data.message || 'Gagal memperbarui buku'));
                    }
                }
            })
            .catch(error => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                console.error('Update error:', error);
                
                let errorMsg = 'Terjadi kesalahan saat memperbarui buku';
                if (error.errors) {
                    const errorList = Object.values(error.errors).flat();
                    errorMsg = 'Validasi gagal:\n' + errorList.join('\n');
                } else if (error.message) {
                    errorMsg = error.message;
                }
                
                if (window.showToast) {
                    window.showToast('Error!', errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
            });
        }

        // Function untuk menghapus buku - SUDAH DIPERBAIKI
        function deleteBook(bookId) {
            if (!confirm('Apakah Anda yakin ingin menghapus buku ini? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }
            
            // Buat FormData untuk method spoofing
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            
            fetch(`/dashboard/buku/${bookId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Tampilkan toast notification
                    if (window.showToast) {
                        window.showToast('Berhasil!', 'Buku berhasil dihapus', 'success');
                    } else {
                        alert('Buku berhasil dihapus!');
                    }
                    
                    closeBookDetailModal();
                    
                    // Reload halaman setelah delay singkat
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    if (window.showToast) {
                        window.showToast('Gagal!', data.message || 'Gagal menghapus buku', 'error');
                    } else {
                        alert(data.message || 'Gagal menghapus buku.');
                    }
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                
                let errorMsg = 'Terjadi kesalahan saat menghapus buku';
                if (error.message) {
                    errorMsg = error.message;
                }
                
                if (window.showToast) {
                    window.showToast('Error!', errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
            });
        }

        // Close modal when clicking outside
        document.getElementById('bookDetailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookDetailModal();
            }
        });

        document.getElementById('editBookModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBookDetailModal();
                closeEditModal();
            }
        });
    </script>
@endsection