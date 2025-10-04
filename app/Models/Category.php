<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'category_description',
        'category_icon',
        'total_books',
        'average_rating',
        'is_active',
    ];

    public function books()
    {
        return $this->hasMany(Book::class, 'category_id', 'category_id');
    }
    public function category()
{
    return $this->belongsTo(Category::class, 'category_id', 'category_id');
}

}
