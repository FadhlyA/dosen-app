<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'storage_limit',
        'storage_used',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    // Tambahkan ini di dalam class User, sebelum tanda } terakhir
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    // Relasi: User punya 1 Profile
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    // Helper: nama lengkap dengan gelar
    public function getFullNameAttribute()
    {
        if ($this->profile) {
            return $this->profile->full_name;
        }
        return $this->name;
    }
    // Storage terpakai dalam format readable
    public function getStorageUsedReadableAttribute()
    {
        return $this->formatBytes($this->storage_used);
    }

    // Storage limit dalam format readable
    public function getStorageLimitReadableAttribute()
    {
        return $this->formatBytes($this->storage_limit);
    }

    // Persentase storage terpakai
    public function getStoragePercentageAttribute()
    {
        if ($this->storage_limit === 0) return 0;
        return round(($this->storage_used / $this->storage_limit) * 100, 1);
    }

    // Cek apakah storage penuh
    public function isStorageFull()
    {
        return $this->storage_used >= $this->storage_limit;
    }

    // Format bytes ke readable
    private function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    // Tambah storage used
    public function addStorageUsed($bytes)
    {
        $this->increment('storage_used', $bytes);
    }

    // Kurangi storage used
    public function reduceStorageUsed($bytes)
    {
        $newValue = max(0, $this->storage_used - $bytes);
        $this->update(['storage_used' => $newValue]);
    }
}
