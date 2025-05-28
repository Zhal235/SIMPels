<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfidTag extends Model
{
    // Tentukan field yang boleh diisi massal
    protected $fillable = [
        'tag_uid',
        'santri_id',
        'pin',
    ];

    /**
     * Relasi: satu tag dimiliki satu santri (opsional)
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }
}
