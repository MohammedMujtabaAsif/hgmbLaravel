<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Demency\Friendships\Traits\Friendable;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use \Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, Friendable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // User's personal details
        'firstNames', 'surname', 'prefName', 'email', 'password', 'phoneNumber', 'city_id', 'gender_id', 'marital_status_id', 'dob', 'numOfChildren', 'bio', 'imageAddress',
        
        'prefMinAge', 'prefMaxAge', 'prefMaxNumOfChildren', 'prefMaritalStatuses',
        //  'prefCities',  'prefGenders',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot', 'created_at', 'updated_at', 'email_verified_at',

        'firstNames', 'surname', 'email', 'phoneNumber', 'city_id', 'gender_id', 'marital_status_id', 'dob', 
        'adminApproved', 'adminUnapprovedMessage', 'adminBanned', 'adminBannedMessage', 'deactivated', 
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'age'
    ];

    public function getAgeAttribute() {
        return (int) Carbon::parse($this->dob)->diff(Carbon::now())->format('%y');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function city()
    {
        return $this->belongsTo(City::class)->select(['id', 'name']);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class)->select(['id', 'name']);
    }

    public function maritalStatus()
    {
        return $this->belongsTo(MaritalStatus::class)->select(['id', 'name']);
    }


    public function prefCities()
    {
        return $this->belongsToMany(City::class)->select(['city_id', 'name']);
    }


    public function prefGenders()
    {
        return $this->belongsToMany(Gender::class)->select(['gender_id','name']);
    }


    public function prefMaritalStatuses()
    {
        return $this->belongsToMany(MaritalStatus::class)->select(['marital_status_id', 'name']);
    }

}
