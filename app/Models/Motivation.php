<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motivation extends Model
{
    use HasFactory;
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];  
    
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($motivation) {
            if ($motivation->active) {
                Motivation::whereActive(true)
                    ->whereType($motivation->type)
                    ->update(['active' => false]);
            }
        });
        
        static::updating(function ($motivation) {
            if ($motivation->active) {
                if ($motivation->getOriginal('active') != $motivation->active) {
                    Motivation::whereActive(true)
                        ->whereType($motivation->type)
                        ->update(['active' => false]);
                }
            }
        });        
    }           
}
