<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'age',
        'phone',
        'dob',
        'gender',
        'profile1',
        'profile2',
        'selfie',
        'status',
        'state',
        'city',
        'gender_filter',
        'radius_filter',
        'from_age_filter',
        'to_age_filter',
        'ref_id',
        'referral_code',
        'device_id',
        'device_type',
        'device_name'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function subscription()
    {
        return $this->hasOne(SubscriptionModel::class); // Assuming a one-to-one relationship
    }
}
