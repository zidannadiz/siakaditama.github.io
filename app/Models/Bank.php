<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            // Cek apakah file ada di storage
            $storagePath = storage_path('app/public/' . $this->logo);
            if (file_exists($storagePath)) {
                return asset('storage/' . $this->logo);
            }
            // Cek apakah file ada di public
            $publicPath = public_path('storage/' . $this->logo);
            if (file_exists($publicPath)) {
                return asset('storage/' . $this->logo);
            }
        }
        // Default: return placeholder atau null
        return null;
    }
}

