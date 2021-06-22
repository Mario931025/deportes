<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    /**
     * Get the student user that owns the promotion.
     */
    public function studentUser()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the instructor user that owns the promotion.
     */
    public function instructorUser()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the grade that owns the promotion.
     */
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }    
}
