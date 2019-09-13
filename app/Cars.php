<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'user_id', 'name', 'make', 'model', 'year', 'color',
    ];

}
