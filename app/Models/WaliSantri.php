<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class WaliSantri extends Authenticatable
{
    use HasApiTokens;
    
    protected $table = 'wali_santri';
    
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address', 'nik', 
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'pekerjaan', 'status'
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'tanggal_lahir' => 'date',
        'password' => 'hashed',
    ];
    
    // Relations
    public function santri()
    {
        return $this->hasMany(Santri::class, 'wali_santri_id');
    }
    
    public function tagihan()
    {
        return $this->hasManyThrough(Tagihan::class, Santri::class, 'wali_santri_id', 'santri_id');
    }
    
    public function perizinan()
    {
        return $this->hasManyThrough(Perizinan::class, Santri::class, 'wali_santri_id', 'santri_id');
    }
}
