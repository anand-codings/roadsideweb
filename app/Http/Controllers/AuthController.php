<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Subscription;
use App\Payment;
use Laravel\Cashier;
use Carbon\Carbon;

class AuthController extends Controller
{
//    Cron Function
        function statusCron(){
        
//Get All subscriptions whose ends date expired        
        $subscriptions  = Subscription::where('status',1)->whereDate('ends_at','<', Carbon::now())->get();
        foreach ($subscriptions as $subscription){
         \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));    
         $subscription_id = $subscription->stripe_id;
         $id = $subscription->user_id;
         $stripe_details = \Stripe\Subscription::retrieve($subscription_id);
         $status = $stripe_details->status;
         
         if($status == 'active'){
             
                $plan_amount = $stripe_details->plan->amount;
                $timestamp = $stripe_details->current_period_end;
                $periods_end =  date('Y-m-d H:i:s',$timestamp);
                    
                $subscription = Subscription::where('user_id',$id)->where('stripe_id',$subscription_id)->first();
                
                $counter = $subscription->counter +1;       
                $subscription->ends_at = $periods_end;
                $subscription->status  = 1;
                $subscription->counter =$counter;
                $subscription->save();


                $stripe_details = \Stripe\Subscription::retrieve($subscription_id);
                $plan_amount = $stripe_details->plan->amount;
                $payement = new Payment();
                $payement->user_id = $id;
                $payement->amount = $plan_amount;
                $payement->charge_id = $subscription_id;
                $payement->save();            
             
         }else{
             $subscription = Subscription::where('user_id',$id)->where('stripe_id',$subscription_id)->first();
             $subscription->status  = 0;
             $subscription->save();
         }
        }
    }
}
