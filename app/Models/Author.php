<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
    protected $table = 'author';

    public function books()
    {
        return $this->belongsTo(Book::class);
    }
    use HasFactory;
}
