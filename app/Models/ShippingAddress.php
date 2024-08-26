<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    public $timestamps = false;
    protected $fillable = ['name','price'];

    public function order(){
        return $this->belongsTo(Order::class);
    }
    public function cart(){
        return $this->belongsTo(Cart::class);
    }


    protected $table = 'shipping_address';
    use HasFactory;
}
