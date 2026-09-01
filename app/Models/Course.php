<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage; // tambah ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'class_name',
        'semester',
        'access_key',
        'description',
        'attendance_formula',
        'attendance_threshold',
    ];

    // Relasi: Course punya banyak Meeting
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    // Relasi: Course dimiliki oleh User (dosen)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Relasi: Course punya banyak GradeComponent
    public function gradeComponents()
    {
        return $this->hasMany(GradeComponent::class);
    }
    // Relasi: Course punya banyak Student
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    // Relasi: Course punya banyak RPS
    public function rps()
    {
        return $this->hasMany(Rps::class);
    }
    // Buat komponen tetap otomatis saat kelas dibuat
    public function createDefaultComponents()
    {
        $defaults = [
            [
                'name'                => 'Tugas',
                'weight'              => 10,
                'is_fixed'            => true,
                'is_assignment_based' => true,
                'is_attendance'       => false,
                'attendance_formula'  => 'hadir_only',
            ],
            [
                'name'                => 'UTS',
                'weight'              => 30,
                'is_fixed'            => true,
                'is_assignment_based' => false,
                'is_attendance'       => false,
                'attendance_formula'  => 'hadir_only',
            ],
            [
                'name'                => 'UAS',
                'weight'              => 40,
                'is_fixed'            => true,
                'is_assignment_based' => false,
                'is_attendance'       => false,
                'attendance_formula'  => 'hadir_only',
            ],
            [
                'name'                => 'Absensi',
                'weight'              => 10,
                'is_fixed'            => true,
                'is_assignment_based' => false,
                'is_attendance'       => true,
                'attendance_formula'  => 'hadir_only',
            ],
        ];

        foreach ($defaults as $component) {
            $this->gradeComponents()->firstOrCreate(
                ['name' => $component['name'], 'course_id' => $this->id],
                $component
            );
        }
    }
    public function gradeLetterConfigs()
    {
        return $this->hasMany(GradeLetterConfig::class)->orderBy('order');
    }

    // Buat konfigurasi nilai huruf default
    public function createDefaultGradeLetters()
    {
        $defaults = [
            ['letter' => 'A', 'min_score' => 85, 'max_score' => 100, 'order' => 1],
            ['letter' => 'B', 'min_score' => 75, 'max_score' => 84.99, 'order' => 2],
            ['letter' => 'C', 'min_score' => 65, 'max_score' => 74.99, 'order' => 3],
            ['letter' => 'D', 'min_score' => 55, 'max_score' => 64.99, 'order' => 4],
        ];

        foreach ($defaults as $default) {
            $this->gradeLetterConfigs()->firstOrCreate(
                ['letter' => $default['letter']],
                $default
            );
        }
    }
    // Ambil nama institusi (dari kelas atau profil dosen)
    public function getInstitutionNameDisplay()
    {
        if ($this->institution_name) {
            return $this->institution_name;
        }
        return $this->user->profile->institution_name ?? 'AMIK Mahaputra Riau';
    }

    // Ambil semua info institusi
    public function getInstitutionInfo()
    {
        $profile = $this->user->profile;
        return [
            'name'    => $this->institution_name ?? ($profile->institution_name ?? 'AMIK Mahaputra Riau'),
            'address' => $profile->institution_address ?? 'Jl. Muchtar Lutfi - Jl. S.M. Amin, Pekanbaru',
            'email'   => $profile->institution_email ?? 'info@amikmahaputra.ac.id',
            'website' => $profile->institution_website ?? 'www.amikmahaputra.ac.id',
            'phone'   => $profile->institution_phone ?? '0853-7164-2326',
            'logo'    => $profile->institution_logo
                         ? Storage::url($profile->institution_logo)
                         : asset('images/logo_kampus.png'),
        ];
    }
}