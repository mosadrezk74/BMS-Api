<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    protected $table = 'cart_items';

    protected $fillable = ['user_id', 'book_id', 'quantity' ,'ship_id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    public function ship(){
        return $this->belongsTo(ShippingAddress::class);
    }
}
