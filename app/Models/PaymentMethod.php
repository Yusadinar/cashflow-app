<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['user_id', 'name', 'balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function transfersOut()
    {
        return $this->hasMany(Transfer::class, 'from_payment_method_id');
    }

    public function transfersIn()
    {
        return $this->hasMany(Transfer::class, 'to_payment_method_id');
    }

    public function getCurrentBalanceAttribute()
    {
        $pmIncome = $this->transactions()->where('type', 'income')->sum('amount');
        $pmExpense = $this->transactions()->where('type', 'expense')->sum('amount');
        $transfersIn = $this->transfersIn()->sum('amount');
        $transfersOut = $this->transfersOut()->sum('amount');

        return $this->balance + $pmIncome - $pmExpense + $transfersIn - $transfersOut;
    }
}
