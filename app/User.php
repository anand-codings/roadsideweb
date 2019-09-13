<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'contact_number' ,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
//    protected $appends = array('name');
    
    public function getSubscription(){
        return $this->hasOne(Subscription::class,'user_id','id');
    }
    
    public function getPaymnet(){
        return $this->hasMany(Payment::class,'user_id','id');
    }
    public function getJob(){
        return $this->hasMany(Jobs::class,'user_id','id')->whereDate('created_at','>=', Carbon::today()->subYear(1));
    }
}
