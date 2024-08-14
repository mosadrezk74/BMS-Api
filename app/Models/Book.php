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

    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'm_n_m', 'book_id', 'author_id');
    }


    use HasFactory;
}

