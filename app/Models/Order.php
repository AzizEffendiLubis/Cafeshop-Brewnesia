<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_code', 'table_id', 'customer_name', 'session_id',
        'subtotal', 'tax', 'total', 'payment_method',
        'payment_status', 'status', 'notes', 'paid_at'
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'tax'      => 'integer',
        'total'    => 'integer',
        'paid_at'  => 'datetime',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(CafeTable::class, 'table_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending'   => '⏳ Menunggu',
            'process'   => '🍳 Diproses',
            'ready'     => '🔔 Siap Diambil',
            'done'      => '✅ Selesai',
            'cancelled' => '❌ Dibatalkan',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'pending'   => 'warning',
            'process'   => 'info',
            'ready'     => 'primary',
            'done'      => 'success',
            'cancelled' => 'danger',
        ][$this->status] ?? 'secondary';
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public static function generateCode(): string
    {
        do {
            $code = 'BN-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('order_code', $code)->exists());
        return $code;
    }
}
