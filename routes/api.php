<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Book;

Route::get('/books', function () {
    // Ambil semua buku yang aktif
    $books = Book::where('is_active', 1)
        ->select('book_id', 'title', 'author', 'cover_url', 'description', 'category', 'average_rating')
        ->get();

    return response()->json([
        'status' => 'success',
        'count' => $books->count(),
        'data' => $books
    ]);
});

Route::get('/books/{id}', function ($id) {
    $book = Book::find($id);

    if (!$book) {
        return response()->json(['status' => 'error', 'message' => 'Buku tidak ditemukan'], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $book
    ]);
});
