<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Country extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'country_code',
        'country_enName',
        'country_arName',
        'country_enNationality',
        'country_arNationality',
    ];

    protected $appends = ['name', 'nationality', 'flag_url', 'timezone'];


    protected function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                $locale = Auth::check() ? Auth::user()->lang : app()->getLocale();
                $locale = in_array($locale, ['ar', 'en']) ? $locale : 'ar';
                return $this->{"country_{$locale}Name"};
            }
        );
    }

    protected function nationality(): Attribute
    {
        return Attribute::make(
            get: function () {
                $locale = Auth::check() ? Auth::user()->lang : app()->getLocale();
                $locale = in_array($locale, ['ar', 'en']) ? $locale : 'ar';
                return $this->{"country_{$locale}Nationality"};
            }
        );
    }

    protected function flagUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->country_code
                ? "https://flagcdn.com/w320/" . strtolower($this->country_code) . ".png"
                : null
        );
    }
    protected function timezone(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->country_code
                ? "https://restcountries.com/v3.1/alpha/" . $this->country_code
                : null
        );
    }
};
