<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Subscription;
use App\Payment;
use Laravel\Cashier;
use Carbon\Carbon;
use App\Admin;
use \Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Session;
use Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Response;
use App\Jobs;


class AdminController extends Controller {

    public function dashboard() {

        $this->data['user_count'] = User::all()->count();
        $this->data['active_subscription'] = Subscription::all()->where('status',1)->count();
        $this->data['inactive_subscription'] = Subscription::all()->where('status',0)->count();
        $this->data['renew_counter'] = Subscription::all()->sum('counter');
        //$this->data['renew_subscription'] = Payment::all()->count();
//        $sub_total = Subscription::all()->sum('amount');
        $this->data['toal_revenue'] = Payment::all()->sum('amount')/100; //Total Revenue
        $this->data['pay_total_year']  = Payment::whereDate('created_at','>=', Carbon::today()->subYear(1))->sum('amount')/100; //Last Year Revenue
        $this->data['pay_total_quater']  = Payment::whereDate('created_at','>=', Carbon::today()->subMonth(3))->sum('amount')/100; //Last Quarter Revenue
        $this->data['pay_total_month'] = Payment::whereDate('created_at', '>=', Carbon::today()->subMonth(1))->sum('amount')/100;//Last Month Days Revenue
        $this->data['pay_total_week'] = Payment::whereDate('created_at', '>=', Carbon::today()->subDays(7))->sum('amount')/100;//Last Seven Days Revenue
        $this->data['pay_total_day']  = Payment::whereDate('created_at', Carbon::today())->sum('amount')/100; //Today Revenue
        $mygetdate = \Carbon\Carbon::today()->subYear(1);
//        dd($this->data['pay_total_year']);
        $active_sub = Subscription::whereDate('created_at','>=', Carbon::today()->subYear(1))->count();
        $inactive_sub = Subscription::whereDate('created_at','>=', Carbon::today()->subYear(1))->where('status',0)->count();
        $this->data['churn_date'] =  round((($active_sub-$inactive_sub)/$active_sub)*100, 2);
//        dd($active_sub);
        $plan_49 = Subscription::all()->where('status',1)->where('stripe_plan','plan_FEsvVtxkrrP14v')->count();
        $plan_89 = Subscription::all()->where('status',1)->where('stripe_plan','plan_FEsxkqVubAiUZz')->count();
        $plan_129 = Subscription::all()->where('status',1)->where('stripe_plan','plan_FEsyKIceDTMp7m')->count();
        $this->data['life_time_value'] = (($plan_49*49*2*10)+($plan_89*89*10)+($plan_129*129*10));
//        dd($life_time_value);
        $this->data['tab'] = 'dashboard';

//        whereDate('created_at', Carbon::now()->subDays(7))
//        whereMonth('created_at', '=', Carbon::now()->subMonth()->month);

        return view('admin.dashboard',$this->data);
    }

    public function postLogin(Request $request) {
        $this->validate($request, ['email' => 'required', 'password' => 'required']);
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('dashboard');
        } else {
            return redirect()->back()->with('error', 'Invalid Email or Password');
        }
    }

    public function logout() {
        if (Auth::guard('admin')->logout()) {
            return redirect('/');
        } else {
            return redirect()->back()->with('error', 'Not Logout');
        }
    }

    public function testCreateCardToken(){
//        \Stripe\Stripe::setApiKey("sk_test_5U2DWCQfgd0issmQbyH3MSOi");
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
       $token=  \Stripe\Token::create([
            'card' => [
                'number' => '4242 4242 4242 4242',
                'exp_month' => 6,
                'exp_year' => 2020,
                'cvc' => '314'
            ]
        ]);
       return $token;
    }
    public function createSubscription(Request $request) {
        $id = $request->id;
        $token = $request->token;
        $user = User::find($id);

        if($user->newSubscription('RoadSide', 'plan_FEsyKIceDTMp7m')->create($token)){

            return redirect()->back()->with('success','Subscription Create Successfully!');

        }
        else{
            return redirect()->back()->with('error','Error while creating subsciption');
        }


    }

//    public function getSubscriptionPlan(){
//        \Stripe\Stripe::setApiKey("sk_test_5U2DWCQfgd0issmQbyH3MSOi");
//
//        $signle_plan['subscription'] = \Stripe\Subscription::retrieve('sub_FEub2ZBGHpBTxS');
//        return view('admin.dashboard',$signle_plan);
//    }

//    public function allSubscription(){
//        \Stripe\Stripe::setApiKey("sk_test_5U2DWCQfgd0issmQbyH3MSOi");
//
////        $signle_plan['subscriptions'] = \Stripe\Subscription::all(['limit' => 3]);
//        $signle_plan['subscriptions'] = \Stripe\Subscription::all();
//        return view('admin.dashboard',$signle_plan);
//
//    }

    public function cancelSubscription($subscription_id){

//            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $sub = \Stripe\Subscription::retrieve($subscription_id);
            $sub->cancel();

            return redirect()->back()->with('success','Subscription cancel successfully!!!');

    }

    public function getusers(){
        $this->data['tab'] = 'users';

        $this->data['title']= 'Users';

//        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $this->data['subscriptions'] = \Stripe\Subscription::all();
        $this->data['users'] = User::with('getSubscription','getJob')->get();
        return view('admin.users',$this->data);
    }


    public function getSubscriptions(){
        /*
         * Old One

        $this->data['tab']= 'subscriptions';
        $this->data['title']= 'Subscriptions';
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
//        \Stripe\Stripe::setApiKey('sk_test_5U2DWCQfgd0issmQbyH3MSOi');
        $this->data['subscriptions'] = \Stripe\Subscription::all();
//        $users = $this->data['subscriptions']->with('getUser')->get();
//        dd($this->data['subscriptions']);

        return view('admin.subscriptions',$this->data);
        */

        $data['tab']= 'subscriptions';
        $data['title']= 'Subscriptions';
//        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
//        $data['subscriptions'] = \Stripe\Subscription::all();
        $data['subscriptions'] = Subscription::with('getUser.getPaymnet')->orderBy('id', 'DESC')->get();
        return view('admin.subscriptions',$data);
    }




//    Edit Admin Profile View
    function editProfileView(){

        $data['tab'] = 'edit_profile';
        $data['title'] = 'Edit My Profile';
        $id = Auth::guard('admin')->user()->id;
        $data['detail'] = Admin::find($id)->first();
        return view('admin.edit-admin-profile', $data);
    }

//    Save Admin Profile Data
    function editProfileData(Request $request){
          $request->validate([
            'full_name'   => 'required|min:1|max:191',
            'email'        => 'required|email|max:191',
            'profile_img'     => 'image|mimes:jpeg,jpg,bmp,png,gif',
        ]);
//        dd($request->full_name);
            //Get Admin ID
            $id = Auth::guard('admin')->user()->id;
            //Check Image Exist
            $img_check          = $request->hasFile('profile_img');
            //Fetch Admin From DB
            $admin = Admin::find($id)->first();
            //Set values against fields
            $admin->full_name = $request->full_name;
            $admin->email = $request->email;
            //Fetch Image
            $old_photo     = 'public/images/'.$admin->profile_pic;
            //If New Image Added than delete old image and insert new image to DB and folder
            if($img_check){

                $image              = $request->file('profile_img');
                $path               = 'public/images/admin/profile_pic/';
                $random             = substr(md5(mt_rand()), 0, 20);
                $filename           = $random . '.' . $image->getClientOriginalExtension();
                $image->move($path,$filename);
                $final_path         = '/admin/profile_pic/'.$filename;
                $admin->profile_pic     = $final_path;
                //Deleting image from folder
                if(File::exists($old_photo)) {
                    File::delete($old_photo);
                }

            }
            $admin->save();
            Session::flash('success', 'Updated successfully');
            return Redirect::to(URL::previous());


    }
//    Chnage Admin Password
    function changePassword(Request $request){

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
        $password  = Auth::guard('admin')->user()->password;
        if (Hash::check($request['current_password'], $password)) {

            $id = Auth::guard('admin')->user()->id;
            $admin = Admin::find($id)->first();
            $admin->password = bcrypt($request->password);

            $admin->save();
//            Session::flash('success', 'Password Updated successfully');
             return Response::json(array('status' => true, 'message' => 'Password Updated successfully'), 200);

        } else {
            //Session::flash('error', 'Invalid Old Password');
            //return Redirect::to(URL::previous());
            return Response::json(array('success' => false, 'message' => 'Invalid Old Password', 'from' => 'invalid'), 400);

        }
    }
        function registerProfile(){
            $data['tab'] = 'register_profile';
            $data['title'] = 'Register';

            return view('users.register_view', $data);
        }
        function register(Request $request){
//            dd($request->all());
            
            $validator = Validator::make($request->all(), [
                       'name' => 'required',
                       'email' => 'required|email|unique:users',
                       'password' => 'required',
                       'c_password' => 'required|same:password',
                       'contact_number' => 'required',

           ]);


            if ($validator->fails()) {
                $errors = implode(', ', $validator->errors()->all());

                 return redirect()->back()->withErrors($validator)->withInput();
            }
           $input = $request->all();

           $input['password'] = bcrypt($input['password']);
           $data['title'] = 'Register';

           $input = $request->all();
           $input['password'] = bcrypt($input['password']);
           $user = User::create($input);
           $user->session_token = $user->createToken('RoadSide')->accessToken;
           $user->save();
           $end_date = '';
           $data['plan'] = $request->plan;
           if($request->plan == '50MilesYear'){
               $planToken = 'plan_FEsyKIceDTMp7m';
               $miles=10;
               $counter=6;
              $end_date = Carbon::now()->addMonths(12)->format('Y-m-d H﻿﻿:i:s');
           } else if($request->plan == '10MilesYear'){
               $planToken = 'plan_FEsxkqVubAiUZz';
               $miles=10;
               $counter=4;
               $end_date = Carbon::now()->addMonths(12)->format('Y-m-d H﻿﻿:i:s');
           } else if($request->plan == '6Months'){
               $planToken = 'plan_FEsvVtxkrrP14v';
               $miles=10;
               $counter=2;
               
               $end_date = Carbon::now()->addMonths(6)->format('Y-m-d H﻿﻿:i:s');
           } else {
               $data['plan'] =  '6Months';
               $planToken = 'plan_FEsvVtxkrrP14v';
               $miles=10;
               $counter=2;
               
               $end_date = Carbon::now()->addMonths(6)->format('Y-m-d H﻿﻿:i:s');
           }

           \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $token=  \Stripe\Token::create([
                 'card' => [
                     'number' => $request->cardnumber,
                     'exp_month' => $request->month,
                     'exp_year' => $request->year,
                     'cvc' => $request->cvc
                 ]
             ]);
           if($get = $user->newSubscription('RoadSide', $planToken)->create($token->id)){
               
                $stripe_details = \Stripe\Subscription::retrieve($get->stripe_id);
                
                $subscription = Subscription::where('stripe_id',$get->stripe_id)->first();
//                dd($subscription);
                $subscription->status = 1;
                $subscription->total_miles = $miles;
                $subscription->counter = $counter;
                $timestamp = date('Y-m-d H﻿﻿:i:s',$stripe_details->current_period_end);
//                dd($timestamp);
                //2020-02-26 11﻿﻿:02:44
                $subscription->ends_at = $end_date;
                
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
                
                return redirect('user_login')->with('success','You have Successfully Subscribed for the '.$data['plan'].' Membership');
//                return view('users.login', $data);
//                return Response::json(array('success' => true, 'message' => 'User Registered Successfully', 'stripe_customer_id'=> $user->stripe_id, 'stripe_charge_id' =>$get->stripe_id ), 200);

           } else {
               User::where('id',$user->id)->delete();
               return Response::json(array('success' => false, 'message' => 'Cant create Stripe Token'), 200);
           }

    }
    public function testDateCompare(){
        $today = Carbon::now()->format('Y-m-d H﻿﻿:i:s');
        $subs = Subscription::whereDate('ends_at','>',$today)->get();
//        dd($subs);
//        echo '<pre>';
        return $subs;
        
    }
}
