<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    protected $table = 'books';

    protected $primaryKey = 'book_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'book_id',
        'isbn',
        'title',
        'author',
        'publisher',
        'publication_year',
        'category_id', // foreign key ke categories table
        'category', // string field untuk backward compatibility
        'description',
        'total_copies',
        'available_copies',
        'stock',
        'available_stock',
        'cover_url',
        'cover_image',
        'status',
        'is_active',
        'average_rating',
        'total_ratings',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'total_copies' => 'integer',
        'available_copies' => 'integer',
        'stock' => 'integer',
        'available_stock' => 'integer',
        'average_rating' => 'decimal:2',
        'total_ratings' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke peminjaman buku
     */
   public function loans()
    {
        return $this->hasMany(\App\Models\Loan::class, 'book_id', 'book_id');
    }

    /**
     * Relasi ke kategori
     */
    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

}
