<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    protected $table = 'loans';
    protected $primaryKey = 'loan_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'book_id',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'notes',
        'kelas_id',
        'student_id',
    ];

    /**
     * Relasi ke User (admin/staf yang memproses peminjaman)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke Student (peminjam)
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Relasi ke Kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    /**
     * Relasi ke Book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id', 'book_id');
    }

    /**
     * Relasi ke BookReturn (jika peminjaman sudah dikembalikan)
     */
    public function return(): HasOne
    {
        return $this->hasOne(BookReturn::class, 'loan_id', 'loan_id');
    }
    public function guru()
{
    return $this->belongsTo(Guru::class, 'guru_id', 'guru_id');
}
}


