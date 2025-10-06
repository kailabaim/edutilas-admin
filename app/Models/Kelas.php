<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'kelas_id';
    public $timestamps = true; // sesuai database ada created_at & updated_at
    
    protected $fillable = [
        'major',
        'grade', 
        'class_number',
        'class_name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship dengan Students
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'kelas_id', 'kelas_id');
    }

    /**
     * Relationship dengan Loans melalui Students
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'kelas_id', 'kelas_id');
    }

    /**
     * Scope untuk kelas aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope berdasarkan jurusan
     */
    public function scopeByMajor($query, $major)
    {
        return $query->where('major', $major);
    }

    /**
     * Scope berdasarkan tingkat
     */
    public function scopeByGrade($query, $grade) 
    {
        return $query->where('grade', $grade);
    }
}