<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'last_name', 'phone',
        'city_id', 'document_number', 'birthday', 'academy_id',
        'photo_profile', 'grade_id', 'active', 'profile_photo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',        
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'birthday',
    ];
    
    /**
     * Get the city that owns the user.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }    
    
    /**
     * Get the academy that owns the user.
     */
    public function academy()
    {
        return $this->belongsTo(Academy::class);
    }
    
    /**
     * Get the role record associated with the user.
     */
    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
    
    /**
     * App\Models\Role relation.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }     

    /**
     * Get the grade record associated with the user.
     */
    public function grade()
    {
        return $this->hasOne(Grade::class, 'id', 'grade_id');
    }
    
    /**
     * Get the social networks record associated with the user.
     */
    public function socialNetwork()
    {
        return $this->hasOne(SocialNetwork::class);
    }
    
    /**
     * Get the device tokens for the user.
     */
    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }
    
    /**
     * Get the promotions for the user.
     */
    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'student_user_id');
    }
    
    /**
     * Get the subscription for the user.
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }    
    
    /**
     * Get the user social network auths for the user.
     */
    public function userSocialNetworkAuth()
    {
        return $this->hasMany(UserSocialNetworkAuth::class);
    }    
    
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function authorizeRoles($roles)
    {
        abort_unless($this->hasAnyRole($roles), 401);
        return true;
    }
    
    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                 return true; 
            }   
        }
        return false;
    }
    
    public function hasRole($role)
    {
        if ($this->roles()->where('name', $role)->first()) {
            return true;
        }
        return false;
    }    
}
