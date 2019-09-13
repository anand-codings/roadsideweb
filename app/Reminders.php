<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reminders extends Model
{
    protected $fillable = [
        'user_id', 'title','purchase_date', 'insurance_date', 'maintainence_date' ,'purchase_time', 'insurance_time', 'maintainence_time', 'purchase_description', 'insurance_description','maintainence_description', 'time_zone',
        	'maintainence_sent', 'insurance_sent', 'purchase_sent', 'all_sent'
    ];
}
