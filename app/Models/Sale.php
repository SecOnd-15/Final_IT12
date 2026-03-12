<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property \Carbon\Carbon $sale_date
 * @property string|null $customer_name
 * @property string|null $customer_contact
 * @property float $subtotal
 * @property float $tax_amount
 * @property float $discount_amount
 * @property float $total_amount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Sale extends Model
{
    //
    protected $fillable = [
        'user_id',
        'sale_date', 
        'customer_name',
        'customer_contact',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
    ];
    
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
