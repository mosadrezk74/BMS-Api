<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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


    use HasFactory;
}

