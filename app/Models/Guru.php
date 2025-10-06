<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';
    protected $primaryKey = 'guru_id';
    
    protected $fillable = [
        'nip',
        'nama_guru',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Accessors
    public function getStatusAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    public function getStatusBadgeClassAttribute()
    {
        return $this->is_active ? 'active' : 'inactive';
    }

    // Search functionality
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('nama_guru', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%");
            });
        }
        return $query;
    }

    // Static methods
    public static function getTotalTeachers()
    {
        return self::count();
    }

    public static function getActiveTeachers()
    {
        return self::active()->count();
    }

    public static function getInactiveTeachers()
    {
        return self::inactive()->count();
    }
}