<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'disabled' => 'boolean',
        'cancellation_date' => 'datetime:Uv',
        'expiration_date' => 'datetime:Uv',
        'purchase_date' => 'datetime:Uv',
        'updation_date' => 'datetime:Uv',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'trial_expired'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user',
        'created_at',
        'updated_at',
        'receipt',
        'raw_data',
        'order_id',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set the subscription status.
     *
     * @param  string  $value
     * @return void
     */
    public function getTrialExpiredAttribute()
    {
        //$roles = $this->user->roles->whereNotIn('id', [3, 4, 5]);

        //if ($roles->count() === 0) {
            $trialExpirationDate = Carbon::create($this->attributes['trial_expiration_date']);
            return $trialExpirationDate <= now();
        //}

        return true;
    }
}
