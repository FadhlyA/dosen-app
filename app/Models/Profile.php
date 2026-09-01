<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'front_title',
    'back_title',
    'nip',
    'nidn',
    'phone',
    'study_program',
    'position',
    'photo',
    'institution_name',
    'institution_address',
    'institution_email',
    'institution_website',
    'institution_phone',
    'institution_logo',
];

    // Relasi: Profile milik User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper: nama lengkap dengan gelar
    public function getFullNameAttribute()
    {
        $name = $this->user->name;
        if ($this->front_title) $name = $this->front_title . ' ' . $name;
        if ($this->back_title)  $name = $name . ', ' . $this->back_title;
        return $name;
    }
    // Ambil info institusi (dari profil)
    public function getInstitutionNameAttribute($value)
    {
        return $value ?? 'Nama Institusi';
    }

    public function getInstitutionAddressAttribute($value)
    {
        return $value ?? 'Alamat Institusi';
    }
}