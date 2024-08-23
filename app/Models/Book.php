<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Book extends Model
{
    public $timestamps = false;
    protected $table='books';
    protected $fillable = [
        'name',
        'image',
        'descr',
        'category_id',
        'author_id',
        'pages',
        'rating',
        'price',

    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
    public function category(){

        return $this->belongsTo(Category::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }
    public function reviews()
{
    return $this->hasMany(Review::class);
}

    use HasFactory;
}

