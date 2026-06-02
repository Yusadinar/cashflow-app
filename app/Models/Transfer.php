<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_payment_method_id',
        'to_payment_method_id',
        'amount',
        'description',
        'transfer_date',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromPaymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'from_payment_method_id');
    }

    public function toPaymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'to_payment_method_id');
    }
}
