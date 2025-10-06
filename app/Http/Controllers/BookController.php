<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Get unique categories for filter dropdown
        $categories = \App\Models\Category::select('category_name')
            ->where('is_active', 1)
            ->orderBy('category_name')
            ->pluck('category_name');
        
        // Pagination with eager loading
        $books = $query->with('categoryRelation')
            ->select([
                'book_id',
                'title', 
                'author',
                'category_id',
                'category',
                'cover_url',
                'cover_image',
                'description',
                'total_copies',
                'available_copies',
                'status',
                'publication_year',
                'stock',
                'available_stock',
                'average_rating',
                'total_ratings',
                'created_at'
            ])
            ->orderBy('created_at', 'asc')
            ->paginate(12);
        
        $books->appends($request->query());
        
        return view('dashboard.buku', compact('books', 'categories'));
    }
    
    // API endpoint untuk mendapatkan detail buku
    public function getDetail($id)
    {
        try {
            $book = Book::with('categoryRelation')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'book' => [
                    'book_id' => $book->book_id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'category' => $book->categoryRelation ? $book->categoryRelation->category_name : ($book->category ?? 'Tidak Dikategorikan'),
                    'category_id' => $book->category_id,
                    'cover_url' => $book->cover_url,
                    'cover_image' => $book->cover_image,
                    'description' => $book->description,
                    'total_copies' => $book->total_copies ?: $book->stock,
                    'available_copies' => $book->available_copies ?: $book->available_stock,
                    'status' => $book->status,
                    'publication_year' => $book->publication_year,
                    'average_rating' => $book->average_rating,
                    'total_ratings' => $book->total_ratings,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }
    }
    
    public function show($id)
    {
        $book = Book::findOrFail($id);
        return view('dashboard.book.show', compact('book'));
    }
    
    public function create()
    {
        $categories = Book::select('category')->distinct()->whereNotNull('category')->orderBy('category')->pluck('category');
        return view('dashboard.book.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:100',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_copies' => 'required|integer|min:1',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'cover_url' => 'nullable|url',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $data = $request->all();
        $data['available_copies'] = $data['total_copies'];
        $data['stock'] = $data['total_copies'];
        $data['available_stock'] = $data['total_copies'];
        $data['status'] = 'available';
        $data['is_active'] = 1;
        $data['average_rating'] = 0.00;
        $data['total_ratings'] = 0;
        
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('books', 'public');
        }
        
        Book::create($data);
        
        return redirect()->route('dashboard.buku')->with('success', 'Buku berhasil ditambahkan');
    }
    
    public function edit($id)
    {
        $book = Book::findOrFail($id);
        $categories = Book::select('category')->distinct()->whereNotNull('category')->orderBy('category')->pluck('category');
        return view('dashboard.book.edit', compact('book', 'categories'));
    }
    
    public function update(Request $request, $id)
    {
        try {
            $book = Book::findOrFail($id);
            
            // Debug: Log the incoming data
            \Log::info('Update request data:', $request->all());
            
            $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:100',
                'category' => 'required|string|max:255',
                'description' => 'nullable|string',
                'total_copies' => 'required|integer|min:1',
                'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
                'cover_url' => 'nullable|url',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'required|in:available,borrowed,damaged,lost'
            ]);
            
            $data = $request->all();
            
            // Clean up data - remove empty strings and convert to proper types
            $data['total_copies'] = (int)$data['total_copies'];
            $data['publication_year'] = !empty($data['publication_year']) ? (int)$data['publication_year'] : null;
            $data['cover_url'] = !empty($data['cover_url']) ? trim($data['cover_url']) : null;
            
            // Update available copies jika total copies berubah
            if ($data['total_copies'] != $book->total_copies) {
                $difference = $data['total_copies'] - $book->total_copies;
                $data['available_copies'] = max(0, $book->available_copies + $difference);
                $data['stock'] = $data['total_copies'];
                $data['available_stock'] = $data['available_copies'];
            }
            
            if ($request->hasFile('cover_image')) {
                if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                    Storage::disk('public')->delete($book->cover_image);
                }
                $data['cover_image'] = $request->file('cover_image')->store('books', 'public');
            }
            
            $book->update($data);
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Buku berhasil diperbarui'
                ]);
            }
            
            return redirect()->route('dashboard.buku')->with('success', 'Buku berhasil diperbarui');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui buku: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }
    
    public function destroy($id)
    {
        try {
            $book = Book::findOrFail($id);
            
            // Cek apakah buku sedang dipinjam
            $activeLoan = $book->loans()->where('status', 'active')->exists();
            if ($activeLoan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus buku yang sedang dipinjam'
                ], 400);
            }
            
            // Hapus gambar cover jika ada
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            
            $book->delete();
            
            // Check if request is AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Buku berhasil dihapus'
                ]);
            }
            
            return redirect()->route('dashboard.buku')->with('success', 'Buku berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus buku'
                ], 500);
            }
            
            return redirect()->route('dashboard.buku')->with('error', 'Terjadi kesalahan saat menghapus buku');
        }
    }
    
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);
        
        return redirect()->route('dashboard.buku')->with('success', 'Data buku berhasil diimpor');
    }
    
    public function export()
    {
        $books = Book::all();
        
        $filename = 'books_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($books) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 'Judul', 'Penulis', 'Kategori', 'Deskripsi', 
                'Total Eksemplar', 'Tersedia', 'Status', 'Tahun Terbit', 
                'Rating', 'Total Rating', 'Dibuat', 'Diupdate'
            ]);
            
            foreach ($books as $book) {
                fputcsv($file, [
                    $book->book_id,
                    $book->title,
                    $book->author,
                    $book->category,
                    $book->description,
                    $book->total_copies ?: $book->stock,
                    $book->available_copies ?: $book->available_stock,
                    $book->status,
                    $book->publication_year,
                    $book->average_rating,
                    $book->total_ratings,
                    $book->created_at->format('Y-m-d H:i:s'),
                    $book->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}