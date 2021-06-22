<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Assistance extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    /**
     * Get the student user that owns the assistance.
     */
    public function studentUser()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the instructor user that owns the assistance.
     */
    public function instructorUser()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the academy that owns the assistance.
     */
    public function academy()
    {
        return $this->belongsTo(Academy::class);
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('c');
    }      
}
