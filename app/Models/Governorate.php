<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'governorate_name_ar',
        'governorate_name_en',
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
