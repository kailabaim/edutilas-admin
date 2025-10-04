@extends('dashboard.layout')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Detail Buku</h1>
        <div class="badge">
            <i class="fas fa-book"></i>
            <span>Informasi Lengkap Buku</span>
        </div>
    </div>

    <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
        <a href="{{ route('dashboard.buku') }}" style="padding: 0.5rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Buku
        </a>
        <a href="{{ route('dashboard.buku.edit', $book->book_id) }}" style="padding: 0.5rem 1rem; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-edit"></i> Edit Buku
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
        <!-- Left Column - Cover & Basic Info -->
        <div class="card">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                @php
                    $coverImage = $book->cover_image ?: $book->cover_url;
                @endphp
                
                @if($coverImage)
                    @php
                        $src = $coverImage;
                        if (!str_starts_with($src, ['http://', 'https://'])) {
                            $src = asset('storage/' . $src);
                        }
                    @endphp
                    <img src="{{ $src }}" alt="{{ $book->title }}" style="max-width: 100%; max-height: 400px; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div style="display: none; background: #f8fafc; height: 400px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #64748b;">
                        <i class="fas fa-book" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <div style="font-weight: 600;">Cover Tidak Tersedia</div>
                    </div>
                @else
                    <div style="background: #f8fafc; height: 400px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #64748b;">
                        <i class="fas fa-book" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <div style="font-weight: 600;">Cover Tidak Tersedia</div>
                    </div>
                @endif
            </div>

            <!-- Status Badge -->
            <div style="text-align: center; margin-bottom: 1.5rem;">
                @php
                    $statusColors = [
                        'available' => ['#dcfce7', '#166534', 'Tersedia'],
                        'borrowed' => ['#fef3c7', '#92400e', 'Dipinjam'],
                        'damaged' => ['#fee2e2', '#991b1b', 'Rusak'],
                        'lost' => ['#fee2e2', '#991b1b', 'Hilang']
                    ];
                    $statusColor = $statusColors[$book->status] ?? ['#f3f4f6', '#6b7280', 'Tidak Diketahui'];
                @endphp
                <span style="background: {{ $statusColor[0] }}; color: {{ $statusColor[1] }}; padding: 0.5rem 1.5rem; border-radius: 20px; font-size: 1rem; font-weight: 600;">{{ $statusColor[2] }}</span>
            </div>

            <!-- Quick Stats -->
            <div style="background: #f8fafc; border-radius: 12px; padding: 1.5rem;">
                <h3 style="color: #374151; font-weight: 700; margin: 0 0 1rem 0; text-align: center;">Statistik Cepat</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div style="text-align: center;">
                        <div style="color: #6b7280; font-size: 0.875rem;">Tersedia</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #059669;">{{ $book->available_copies ?: $book->available_stock ?: 0 }}</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="color: #6b7280; font-size: 0.875rem;">Total Stok</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #3b82f6;">{{ $book->total_copies ?: $book->stock ?: 0 }}</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="color: #6b7280; font-size: 0.875rem;">Rating</div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: #f59e0b;">
                            @if($book->average_rating > 0)
                                <i class="fas fa-star"></i> {{ number_format($book->average_rating, 1) }}
                            @else
                                <span style="color: #6b7280;">-</span>
                            @endif
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <div style="color: #6b7280; font-size: 0.875rem;">Reviews</div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: #8b5cf6;">{{ $book->total_ratings ?: 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Detailed Info -->
        <div class="card">
            <div style="margin-bottom: 2rem;">
                <h1 style="color: #1e293b; font-weight: 700; font-size: 2rem; margin: 0 0 1rem 0; line-height: 1.2;">{{ $book->title }}</h1>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="color: #64748b; font-size: 1.1rem;">
                        <i class="fas fa-user" style="margin-right: 0.5rem;"></i>
                        <strong>Penulis:</strong> {{ $book->author }}
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="color: #64748b; font-size: 1rem;">
                        <i class="fas fa-tag" style="margin-right: 0.5rem;"></i>
                        <strong>Kategori:</strong> {{ $book->category ?: 'Tidak Dikategorikan' }}
                    </div>
                    @if($book->publication_year)
                        <div style="color: #64748b; font-size: 1rem;">
                            <i class="fas fa-calendar" style="margin-right: 0.5rem;"></i>
                            <strong>Tahun:</strong> {{ $book->publication_year }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Description -->
            @if($book->description)
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: #374151; font-weight: 700; margin: 0 0 1rem 0;">Deskripsi</h3>
                    <div style="color: #4b5563; line-height: 1.6; padding: 1rem; background: #f9fafb; border-left: 4px solid #667eea; border-radius: 0 8px 8px 0;">
                        {{ $book->description }}
                    </div>
                </div>
            @endif

            <!-- Detailed Information Grid -->
            <div style="margin-bottom: 2rem;">
                <h3 style="color: #374151; font-weight: 700; margin: 0 0 1rem 0;">Informasi Detail</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                            <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">ID Buku</div>
                            <div style="font-weight: 700; color: #1e293b;">{{ $book->book_id }}</div>
                        </div>
                    </div>
                    <div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                            <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">Status Aktif</div>
                            <div style="font-weight: 700; color: {{ $book->is_active ? '#059669' : '#dc2626' }};">
                                {{ $book->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                            <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">Ditambahkan</div>
                            <div style="font-weight: 700; color: #1e293b;">{{ $book->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    <div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem;">
                            <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">Terakhir Diupdate</div>
                            <div style="font-weight: 700; color: #1e293b;">{{ $book->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Details -->
            @if($book->total_ratings > 0)
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: #374151; font-weight: 700; margin: 0 0 1rem 0;">Rating & Review</h3>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                            <div style="font-size: 3rem; font-weight: 700; color: #f59e0b;">{{ number_format($book->average_rating, 1) }}</div>
                            <div>
                                <div style="display: flex; gap: 0.25rem; margin-bottom: 0.5rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star" style="color: {{ $i <= floor($book->average_rating) ? '#f59e0b' : '#d1d5db' }};"></i>
                                    @endfor
                                </div>
                                <div style="color: #6b7280; font-size: 0.875rem;">Berdasarkan {{ $book->total_ratings }} rating</div>
                            </div>
                        </div>
                        <div style="background: #fff; border-radius: 6px; padding: 1rem;">
                            <div style="color: #374151; font-weight: 600; margin-bottom: 0.5rem;">Distribusi Rating</div>
                            <div style="color: #6b7280; font-size: 0.875rem;">Detail distribusi rating akan ditampilkan di sini</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="{{ route('dashboard.buku.edit', $book->book_id) }}" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-edit"></i> Edit Buku
                </a>
                <button onclick="printBook()" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-print"></i> Print Info
                </button>
                <button onclick="shareBook()" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-share"></i> Bagikan
                </button>
                <button onclick="deleteBook({{ $book->book_id }})" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-trash"></i> Hapus Buku
                </button>
            </div>
        </div>
    </div>

    <script>
        function printBook() {
            window.print();
        }

        function shareBook() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $book->title }}',
                    text: 'Buku: {{ $book->title }} oleh {{ $book->author }}',
                    url: window.location.href
                });
            } else {
                // Fallback copy to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Link berhasil disalin ke clipboard!');
                });
            }
        }

        function deleteBook(bookId) {
            if (confirm('Apakah Anda yakin ingin menghapus buku "{{ $book->title }}"?\n\nTindakan ini tidak dapat dibatalkan.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/dashboard/buku/${bookId}`;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                
                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <!-- Print Styles -->
    <style media="print">
        .page-header, nav, button, .fas {
            display: none !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
        }
    </style>
@endsection