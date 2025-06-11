<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'served_by',
        'notes',
        'order_date'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'order_date' => 'datetime'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function servedByUser()
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    // Generate nomor order otomatis
    public static function generateOrderNumber()
    {
        $today = date('Ymd');
        $lastOrder = self::whereDate('created_at', today())
                        ->orderBy('id', 'desc')
                        ->first();
        
        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return 'ORD' . $today . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('order_status', $status);
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('order_date', $date);
    }

    // Accessor untuk format total
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
}
