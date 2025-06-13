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
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'bukti',
        'status',
        'alasan_ditolak',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'approved_at' => 'datetime',
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
}
