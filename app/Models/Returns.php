<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Returns extends Model
{
    use HasFactory;

    protected $table = 'returns';
    protected $primaryKey = 'id';

    protected $fillable = [
        'return_code',
        'loan_id',
        'book_id',
        'return_date',
        'days_late',
        'fine_amount',
        'book_condition',
        'notes',
        'kelas_id',
        'student_id',
        'guru_id'
    ];

    protected $casts = [
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
        'days_late' => 'integer'
    ];

    // Relationships
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id', 'book_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    // Accessor untuk format tanggal Indonesia
    public function getFormattedReturnDateAttribute()
    {
        return $this->return_date ? $this->return_date->format('d/m/Y') : null;
    }

    // Accessor untuk format mata uang Indonesia
    public function getFormattedFineAmountAttribute()
    {
        return 'Rp ' . number_format($this->fine_amount, 0, ',', '.');
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('return_date', [$startDate, $endDate]);
    }

    // Scope untuk filter berdasarkan kondisi buku
    public function scopeByBookCondition($query, $condition)
    {
        return $query->where('book_condition', $condition);
    }

    // Scope untuk yang ada denda
    public function scopeWithFine($query)
    {
        return $query->where('fine_amount', '>', 0);
    }

    // Scope untuk hari ini
    public function scopeToday($query)
    {
        return $query->whereDate('return_date', today());
    }

    // Scope untuk bulan ini
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('return_date', now()->month)
                    ->whereYear('return_date', now()->year);
    }

    // Static method untuk mendapatkan total denda
    public static function getTotalFines()
    {
        return static::sum('fine_amount');
    }

    // Static method untuk mendapatkan total pengembalian hari ini
    public static function getTodayReturnsCount()
    {
        return static::today()->count();
    }
}