<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Academy extends Model
{
    use HasFactory;
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    /**
     * Get the country that owns the academy.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    /**
     * Get the city that owns the academy.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }     
}
