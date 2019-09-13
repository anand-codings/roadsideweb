<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public function getUser(){
        return $this->hasOne(User::class,'id','user_id');
    }   
    
}
