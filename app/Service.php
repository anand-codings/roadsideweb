<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;

class Service extends Model
{
    use HasApiTokens, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $fillable = [
//        'name', 'user_id', 'sub_id', 'job_id' , 'miles_covered', 'total_miles', 'status', 'amount', 'created_at', 'updated_at'
//    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
//    protected $hidden = [
//        'password', 'remember_token',
//    ];

    public function getSubscription(){
        return $this->hasOne(Subscription::class,'id','sub_id');
    }

//    public function getPaymnet(){
//        return $this->hasMany(Payment::class,'user_id','id');
//    }
    public function getJob(){
        return $this->hasOne(Jobs::class,'id','job_id')->whereDate('created_at','>=', Carbon::today()->subYear(1));
    }
}
