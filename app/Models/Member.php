<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'member_id',
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'status',
        'registration_date'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'registration_date' => 'date',
    ];

    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(BookReturn::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'suspended' => 'Ditangguhkan',
            default => 'Tidak Diketahui'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'suspended' => 'red',
            default => 'gray'
        };
    }
}
