<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'm_n_m', 'author_id', 'book_id');
    }
    use HasFactory;
}
