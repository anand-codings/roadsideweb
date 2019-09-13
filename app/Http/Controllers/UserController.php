<?php

namespace App\Http\Controllers;

use App\Service;
use App\Subscription;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Payment;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Session;
use Redirect;
use Response;

class UserController extends Controller {



    public function postLogin(Request $request) {

        $this->validate($request, ['email' => 'required', 'password' => 'required']);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('userdashboard');
        } else {
            return redirect()->back()->with('error', 'Invalid Email or Password');
        }
    }

    public function userDashboard() {
        $data['title'] = 'User Dashboard';
        $data['tab'] = 'User ';

        return view('users.user_dashboard', $data);
    }

    public function logout() {
        if (Auth::logout()) {
            return redirect('/user_login');
        } else {
            return redirect()->back()->with('error', 'Not Logout');
        }
    }

    public function getSubscription() {

        $id = Auth::id();
        $data['tab'] = 'subscription';
        $data['subscription'] = User::where('id', $id)->with('getSubscription', 'getPaymnet')->first();
       
        return view('users.subscription', $data);

    }

    public function cancelSubscription() {


        $id = Auth::id();
        $user = User::where('id', $id)->with('getSubscription')->first();


        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $sub = \Stripe\Subscription::retrieve($user->getSubscription->stripe_id);
        $sub->cancel();

        Subscription::where('user_id', $id)->delete();

        $data['tab'] = 'subscription';
        $data['subscription'] = User::where('id', $id)->with('getSubscription', 'getPaymnet')->first();
        return redirect('usersubscription');
    }

    public function addSubscriptionView() {
        $id = Auth::id();
        $data['tab'] = 'add_subscription_view';
        $data['subscription'] = User::where('id', $id)->with('getSubscription', 'getPaymnet')->first();
        return view('users.subscription_view', $data);
    }

    public function createSubscription($plan) {
        $id = Auth::id();
        $user = User::find($id);
//        dd($id);
        $data['tab'] = 'subscription';
        if ($plan == '50MilesYear') {
            $planToken = 'plan_FEsyKIceDTMp7m';
            $miles=10;
               $counter=6;
        } else if ($plan == '10MilesYear') {
            $planToken = 'plan_FEsxkqVubAiUZz';
            $miles=10;
               $counter=4;
        } else if ($plan == '6Months') {
            $planToken = 'plan_FEsvVtxkrrP14v';
            $miles=10;
               $counter=2;
        }

        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

//       $token= \Stripe\Subscription::create();
            if ($get = \Stripe\Subscription::create([
                        "customer" => $user->stripe_id,
                        "items" => [
                            [
                                "plan" => $planToken,
                            ],
                        ]
                    ])) {

//                $stripe_details = \Stripe\Subscription::retrieve($get->stripe_id);

                $data['amount'] = $plan_amount = $get->plan->amount;
                $payement = new Payment();
                $payement->user_id = $user->id;
                $payement->amount = $plan_amount;
                $payement->charge_id = $get->id;
                $payement->save();
                $subscription = new Subscription;
                $subscription->name = 'RoadSide';
                $subscription->user_id = $id;
                $subscription->stripe_id = $get->id;
                $subscription->stripe_plan = $get->plan->id;
                $subscription->total_miles = $miles;
                $subscription->counter = $counter;
                $subscription->status = 1;
                $timestamp = $get->current_period_end;
                $subscription->ends_at =date('Y-m-d H﻿﻿:i:s', $timestamp);
//                $subscription->trial_ends_at =date('Y-m-d H﻿﻿', $timestamp);
//                $subscription->ends_at =date('Y-m-d H﻿﻿', $timestamp);
                $subscription->quantity = $get->quantity;
                $subscription->save();
                $data['stripe_detail'] = $get;
                $data['success'] = true;
                $data['user'] = $user;


//                return Response::json(array('success' => true, 'message' => 'User Registered Successfully', 'stripe_customer_id'=> $user->stripe_id, 'stripe_charge_id' =>$get->stripe_id ), 200);
            }
        } catch (\Stripe\Error\Card $e) {
//                Session::flash('error', $e->getMessage());
//                return redirect()->back()->withInput();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Stripe\Error\RateLimit $e) {

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Stripe\Error\InvalidRequest $e) {

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Stripe\Error\Authentication $e) {

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Stripe\Error\ApiConnection $e) {

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Stripe\Error\Base $e) {

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        return redirect('usersubscription');
    }

    public function upgradeSubscription($plan) {

        $id = Auth::id();
        $user = User::where('id', $id)->with('getSubscription')->first();
        $subscription = Subscription::where('user_id', $id)->first();
        $counter_s=(int)$subscription->counter;
        
        if ($user->getSubscription->stripe_plan == 'plan_FEsyKIceDTMp7m') {
            $oldcounter=6;
        } else if ($user->getSubscription->stripe_plan == 'plan_FEsxkqVubAiUZz') {
            $oldcounter=4;
        } else if ($user->getSubscription->stripe_plan == 'plan_FEsvVtxkrrP14v') {
            $oldcounter=2;
        }
        
        if ($plan == '50MilesYear') {
            $planToken = 'plan_FEsyKIceDTMp7m';
            $miles=10;
            $counter=6;
            
        } else if ($plan == '10MilesYear') {
            $planToken = 'plan_FEsxkqVubAiUZz';
            $miles=10;
            $counter=4;
            
        } else if ($plan == '6Months') {
            $planToken = 'plan_FEsvVtxkrrP14v';
            $miles=10;
            $counter=2;
        }
        
        $amount_off = 0;
        
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $sub = \Stripe\Subscription::retrieve($user->getSubscription->stripe_id);
        
        if($user->getSubscription->counter > 0 && $user->getSubscription->counter != $oldcounter){
            
            $amount_off = (($sub->plan->amount/100) / $oldcounter) * $user->getSubscription->counter; //(1 - ($oldcounter / $user->getSubscription->counter )) * ($sub->plan->amount/100);
        } else if($user->getSubscription->counter == 0) {
            $amount_off = 0;
        } else {
            $amount_off = $sub->plan->amount/100;
        }
        
       $old_sub_count = $user->getSubscription->counter ;
        if($user->getSubscription->counter != 0) {
            try {
                
                $coupon = \Stripe\Coupon::create([
                    'duration' => 'once',
                    'id' => 'discount-upgrade',
                    'amount_off' => $amount_off*100,
                    'currency' => 'usd',
                ]); 
                
            } catch (Exception $e) {
                $coupon = \Stripe\Coupon::retrieve('discount-upgrade');
                $coupon->delete();
                $coupon = \Stripe\Coupon::create([
                    'duration' => 'once',
                    'id' => 'discount-upgrade',
                    'amount_off' => $amount_off*100,
                    'currency' => 'usd',
                ]); 
            } catch (\Stripe\Error\InvalidRequest $e) { 
                $coupon = \Stripe\Coupon::retrieve('discount-upgrade');
                $coupon->delete();
                $coupon = \Stripe\Coupon::create([
                    'duration' => 'once',
                    'id' => 'discount-upgrade',
                    'amount_off' => $amount_off*100,
                    'currency' => 'usd',
                ]); 
            }
        } 
        
        
        
        $sub->cancel();
        Subscription::where('user_id', $id)->delete();
        $data['tab'] = 'subscription';
        try {
//       $token= \Stripe\Subscription::create();
            if($old_sub_count != 0){
            if ($get = \Stripe\Subscription::create([
                        "customer" => $user->stripe_id,
                        "items" => [
                            [
                                "plan" => $planToken,
                            ],
                        ],
                        "coupon" => 'discount-upgrade',
                
                    ])) {

//                $stripe_details = \Stripe\Subscription::retrieve($get->stripe_id);
                $data['amount'] = $plan_amount = $get->plan->amount;
                $payement = new Payment();
                $payement->user_id = $user->id;
                $payement->amount = $plan_amount;
                $payement->charge_id = $get->id;
                $payement->save();
                $subscription = new Subscription;
                $subscription->name = 'RoadSide';
                $subscription->user_id = $id;
                $subscription->stripe_id = $get->id;
                $subscription->stripe_plan = $get->plan->id;
                $subscription->quantity = $get->quantity;
                $timestamp = $get->current_period_end;
//                $subscription->trial_ends_at =date('Y-m-d H﻿﻿', $timestamp);
                $subscription->ends_at =date('Y-m-d H﻿﻿:i:s', $timestamp);
                $subscription->total_miles = $miles;
                $subscription->counter = $counter;
                $subscription->status = 1;
                $subscription->save();
                $data['stripe_detail'] = $get;
                $data['success'] = true;
                $data['user'] = $user;

//                return Response::json(array('success' => true, 'message' => 'User Registered Successfully', 'stripe_customer_id'=> $user->stripe_id, 'stripe_charge_id' =>$get->stripe_id ), 200);
            }
            } else {
                if ($get = \Stripe\Subscription::create([
                        "customer" => $user->stripe_id,
                        "items" => [
                            [
                                "plan" => $planToken,
                            ],
                        ]
                
                    ])) {

//                $stripe_details = \Stripe\Subscription::retrieve($get->stripe_id);
                $data['amount'] = $plan_amount = $get->plan->amount;
                $payement = new Payment();
                $payement->user_id = $user->id;
                $payement->amount = $plan_amount;
                $payement->charge_id = $get->id;
                $payement->save();
                $subscription = new Subscription;
                $subscription->name = 'RoadSide';
                $subscription->user_id = $id;
                $subscription->stripe_id = $get->id;
                $subscription->stripe_plan = $get->plan->id;
                $subscription->quantity = $get->quantity;
                $timestamp = $get->current_period_end;
//                $subscription->trial_ends_at =date('Y-m-d H﻿﻿', $timestamp);
                $subscription->ends_at =date('Y-m-d H﻿﻿:i:s', $timestamp);
                $subscription->total_miles = $miles;
                $subscription->counter = $counter;
                $subscription->status = 1;
                $subscription->save();
                $data['stripe_detail'] = $get;
                $data['success'] = true;
                $data['user'] = $user;

//                return Response::json(array('success' => true, 'message' => 'User Registered Successfully', 'stripe_customer_id'=> $user->stripe_id, 'stripe_charge_id' =>$get->stripe_id ), 200);
            }
            }
        } catch (\Stripe\Error\Card $e) {
//                Session::flash('error', $e->getMessage());
//                return redirect()->back()->withInput();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Stripe\Error\RateLimit $e) {

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Stripe\Error\InvalidRequest $e) {

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Stripe\Error\Authentication $e) {

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Stripe\Error\ApiConnection $e) {

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Stripe\Error\Base $e) {

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        return redirect('usersubscription');

        Subscription::where('user_id',$id)->delete();

        $data['tab']='subscription';
        $data['subscription']=User::where('id',$id)->with('getSubscription','getPaymnet')->first();
        return redirect('usersubscription');
    }

    //    Edit User Profile
    function editUserProfile()
    {

        $data['tab'] = 'user_edit_profile';
        $data['title'] = 'Edit My Profile';
        $data['detail'] = User::where('id',Auth::user()->id)->first();
        return view('users.user_edit_profile_view', $data);
    }

    //    Save user Profile Data
    function updateUserProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'required|min:1|max:191',
            'email' => 'required|email|max:191',
            'profile_img' => 'image|mimes:jpeg,jpg,bmp,png,gif',
        ]);
        $id = Auth::id();
        //Check Image Exist
        $img_check = $request->hasFile('profile_img');
        //Fetch Admin From DB
        $data['user'] = User::where('id', Auth::user()->id)->first();
        //Set values against fields
        $data['user']->name = $request->full_name;
        //verify user chanage email or not
        if($request->email != '')
        {
            if($data['user']->email  != $request->email)
            {
                $data['user']->email == $request->email;
            }
        }
        $data['user']->email = $request->email;
        //Fetch Image
        $old_photo = 'public/images/' . $data['user']->avatar;

        //If New Image Added than delete old image and insert new image to DB and folder
        if ($img_check) {
            $image = $request->file('profile_img');
            $path = 'public/images/users/profile_pic/';
            $random = substr(md5(mt_rand()), 0, 20);
            $filename = $random . '.' . $image->getClientOriginalExtension();
            $image->move($path, $filename);
            $final_path = '/users/profile_pic/' . $filename;
            $data['user']->avatar = $final_path;
            //Deleting image from folder
            if (File::exists($old_photo)) {
                File::delete($old_photo);
            }

        }
        $data['user']->save();
        Session::flash('success', 'Updated successfully');
        return Redirect::to(URL::previous());
    }
    //    Chnage user Password
    function changeUserPassword(Request $request){
        // Setup the validator
        $message =['password.required'=> 'The new password field is required.'];
        $rules = array('current_password' => 'required', 'password' => 'min:5|required|confirmed|max:191','password_confirmation' => 'required|max:191', );
        $validator = Validator::make(Input::all(), $rules,$message);
        // Validate the input and return correct response
        if ($validator->fails())
        {   $errors = $validator->getMessageBag()->toArray();
            return Response::json(array(
                'status' => false,
                'message' => $errors,
                'from' => 'validator'

            ), 400); // 400 being the HTTP code for an invalid request.
        }
        $password  = Auth::user()->password;
        if (Hash::check($request['current_password'], $password)) {

            $id = Auth::user()->id;
            $user = User::where('id',$id)->first();
            $user->password = bcrypt($request->password);
            $user->save();
//            Session::flash('success', 'Password Updated successfully');
            return Response::json(array('status' => true, 'message' => 'Password Updated successfully'), 200);

        } else {
            //Session::flash('error', 'Invalid Old Password');
            //return Redirect::to(URL::previous());
            return Response::json(array('success' => false, 'message' => 'Invalid Old Password', 'from' => 'invalid'), 400);

        }
    }

    //Services
    public function getServices()
    {
        $data['title']= 'User Dashboard';
        $data['tab']='user_services';
        $data['services']= Service::where('user_id', Auth::user()->id)->get();

        return view('users.show_services_to_user', $data);

    }
     public function newSubscription(Request $request) {
       
        $id = Auth::id();
        $user = User::where('id', $id)->first();
//         $subscription = Subscription::where('user_id', $id)->first();
//     $toatal_miles=(int)$subscription->total_miles;
//     
//        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
//        $sub = \Stripe\Subscription::retrieve($user->getSubscription->stripe_id);
//        $sub->cancel();
       
        
//        Subscription::where('user_id', $id)->delete();
        $data['tab'] = 'subscription';
        if ($request->plan == '50MilesYear') {
            $planToken = 'plan_FEsyKIceDTMp7m';
              $miles=10;
              $counter=6;
        } else if ($request->plan == '10MilesYear') {
            $planToken = 'plan_FEsxkqVubAiUZz';
              $miles=10;
              $counter=4;
        } else if ($request->plan == '6Months') {
            $planToken = 'plan_FEsvVtxkrrP14v';
              $miles=10;
              $counter=2;
        }
         \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $token=  \Stripe\Token::create([
                 'card' => [
                     'number' => $request->number,
                     'exp_month' => $request->date,
                     'exp_year' => $request->year,
                     'cvc' => $request->cvc
                 ]
             ]);
           if($get = $user->newSubscription('RoadSide', $planToken)->create($token->id)){
               
               
                $stripe_details = \Stripe\Subscription::retrieve($get->stripe_id);
                
                $subscription = Subscription::where('stripe_id',$get->stripe_id)->first();
                $subscription->total_miles = $miles;
                $subscription->counter = $counter;
                $timestamp = $stripe_details->current_period_end;
                $subscription->ends_at = date('Y-m-d H﻿﻿:i:s', $timestamp);
                $subscription->save();
               
                $data['amount'] = $plan_amount = $stripe_details->plan->amount;
                $payement = new Payment();
                $payement->user_id =  $user->id;
                $payement->amount = $plan_amount;
                $payement->charge_id = $get->stripe_id;
                $payement->save();
                
                
                $data['stripe_detail'] = $get;
                $data['success'] = true;
                $data['user'] = $user;

//                return view('users.login', $data);
//                return Response::json(array('success' => true, 'message' => 'User Registered Successfully', 'stripe_customer_id'=> $user->stripe_id, 'stripe_charge_id' =>$get->stripe_id ), 200);
           }
//           } else {
//               User::where('id',$user->id)->delete();
//               return Response::json(array('success' => false, 'message' => 'Cant create Stripe Token'), 200);
//           }

    
  
//        return redirect('usersubscription');

//        Subscription::where('user_id',$id)->delete();
//
//        $data['tab']='subscription';
//        $data['subscription']=User::where('id',$id)->with('getSubscription','getPaymnet')->first();
        return redirect('usersubscription');
    }

}
