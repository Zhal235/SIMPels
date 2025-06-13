<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Perizinan extends Model
{
    use HasFactory;

    protected $table = 'perizinan';

    protected $fillable = [
        'santri_id',
        'jenis_izin',
        'keperluan',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'bukti',
        'lampiran',
        'status',
        'alasan_ditolak',
        'catatan_admin',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'approved_at' => 'datetime',
    ];

    protected $appends = [
        'durasi_hari',
        'santri_nama'
    ];

    /**
     * Get the santri that owns the perizinan.
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    /**
     * Get the creator of the perizinan.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the approver of the perizinan.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Approve the perizinan.
     *
     * @param int $approverId
     * @return bool
     */
    public function approve($approverId)
    {
        $this->status = 'disetujui';
        $this->approved_by = $approverId;
        $this->approved_at = Carbon::now();
        return $this->save();
    }

    /**
     * Reject the perizinan.
     *
     * @param int $approverId
     * @param string $reason
     * @return bool
     */
    public function reject($approverId, $reason)
    {
        $this->status = 'ditolak';
        $this->approved_by = $approverId;
        $this->approved_at = Carbon::now();
        $this->alasan_ditolak = $reason;
        return $this->save();
    }

    /**
     * Check if the perizinan is active (approved and current date is within the izin period).
     *
     * @return bool
     */
    public function isActive()
    {
        if ($this->status !== 'disetujui') {
            return false;
        }

        $today = Carbon::today();
        return $today->between($this->tanggal_mulai->startOfDay(), $this->tanggal_selesai->endOfDay());
    }

    /**
     * Mendapatkan durasi izin dalam hari
     */
    public function getDurasiHariAttribute()
    {
        if ($this->tanggal_mulai && $this->tanggal_selesai) {
            return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
        }
        
        return 1; // Default 1 hari jika tanggal tidak lengkap
    }

    /**
     * Mendapatkan nama santri
     */
    public function getSantriNamaAttribute()
    {
        return $this->santri ? $this->santri->nama_santri : null;
    }
}
