<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'student_id';
    public $timestamps = true;

    protected $fillable = [
        'nis',
        'full_name',
        'kelas_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'student_id', 'student_id');
    }

    // Scope untuk student aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}