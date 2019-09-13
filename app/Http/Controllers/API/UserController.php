<?php

// namespace App\Http\Controllers;

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\User;
use App\Cars;
use App\Subscription;
use App\Payment;
use Illuminate\Support\Facades\Auth;
use Validator;
use File;
use Carbon\Carbon;
use App\Jobs;
use App\Service;

class UserController extends Controller {

    public $successStatus = 200;

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) {
//        dd($request->all());
        $validator = Validator::make($request->all(), [
                    'email' => 'required|email|exists:users',
                    'password' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError('Invalid Username or Password', 401);
            // return response()->json(['error' => $validator->errors()], 401);
        }

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            // $user->token = bcrypt(time());
            // $updatedUser = User::find(Auth::user()->id);
            // $user->last_session = str_random(15);
            $user->session_token = $user->createToken('RoadSide')->accessToken;
//            dd($user->session_token);
            $user->save();
            $id = $user->id;
            $user['data'] = User::with('getSubscription')->where('id', $id)->orderBy('id', 'DESC')->first();
            
            if(!empty($user['data']->getSubscription)){
                $plan = $user['data']->getSubscription->stripe_plan;
                
                if($plan == 'plan_FEsyKIceDTMp7m'){
                    $user['data']->getSubscription->level = 'Level 3';
                } else if($plan == 'plan_FEsxkqVubAiUZz'){
                    $user['data']->getSubscription->level = 'Level 2';
                } else if($plan == 'plan_FEsvVtxkrrP14v'){
                    $user['data']->getSubscription->level = 'Level 1';
                }
            }
            $user['data']->session_token = $user->session_token;
            $success = $user['data'];
            return sendSuccess('Login Successfull', $success);
        } else {
            return sendError('Login Failed.', 401);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'password' => 'required',
                    'c_password' => 'required|same:password',
                    'contact_number' => 'required',
                    
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages()->all();
            $messages = join("\n", $messages);
            return sendError($messages, 401);
        }

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user->session_token = $user->createToken('RoadSide')->accessToken;
        $user->save();
        $id   = $user->id;
        $user['data'] = User::with('getSubscription')->where('id', $id)->orderBy('id', 'DESC')->get();
        $success = $user;

        return sendSuccess('Registeration Successfull', $success);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details() {
        $user = Auth::user();
        $id   = $user->id;
        $data['data'] = User::with('getSubscription')->where('id', $id)->orderBy('id', 'DESC')->first();
        if(!empty($data['data']->getSubscription)){
            $plan = $data['data']->getSubscription->stripe_plan;

            if($plan == 'plan_FEsyKIceDTMp7m'){
                $data['data']->getSubscription->level = 'Level 3';
            } else if($plan == 'plan_FEsxkqVubAiUZz'){
                $data['data']->getSubscription->level = 'Level 2';
            } else if($plan == 'plan_FEsvVtxkrrP14v'){
                $data['data']->getSubscription->level = 'Level 1';
            }
        }
            
        return sendSuccess('Current User Detail', $data['data']);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request) {
        $request->user()->token()->revoke();
        return response()->json([
                    'message' => 'Successfully logged out'
        ]);
    }

    public function updateUserProfile(Request $request) {
        $validator = Validator::make($request->all(), [
                    'new_username' => 'required',
                    'contact_number' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError($validator->errors(), 401);
        }
        $user = User::find(Auth::user()->id);
        $user->name = $request->new_username;
        $user->contact_number = $request->contact_number;
        $user->save();
        return sendSuccess('Profile Updated Successfully', $user);
    }

    public function updateAvatar(Request $request) {

        $validator = Validator::make($request->all(), [
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return sendError($validator->errors(), 401);
        }

        $user = User::find(Auth::user()->id);
        File::delete(public_path() . '/svg/' . $user->avatar);
        $image = $request->file('image');
        $filename = str_random(10) . '.' . $image->getClientOriginalExtension();
        $image->move(public_path() . '/svg/', $filename);
        $user->avatar = $filename;
        $user->save();
        return sendSuccess('Avatar Successfully Changed', $user);
    }

    function changePassword(Request $request) {
        $validator = Validator::make($request->all(), [
                    'current_password' => 'required',
                    'password' => 'required'
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages()->all();
            $messages = join("\n", $messages);
            return sendError($messages, 405);
        }
        $password = Auth::user()->password;
        if (Hash::check($request['current_password'], $password)) {
            $newpass = Hash::make($request['password']);
            User::where('id', Auth::user()->id)->update(['password' => $newpass]);
            return sendSuccess('Password updated successfully!', null);
        } else {
            return sendError('Invalid old password!', 405);
        }
    }

    public function create_subscription(Request $request) {
        $validator = Validator::make($request->all(), [
                    'token' => 'required',
                    'plan_id' => 'required'
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages()->all();
            $messages = join("\n", $messages);
            return sendError($messages, 405);
        }

        $id = Auth::user()->id;
        $token = $request->token;
        $plan_id = $request->plan_id;
        $user = User::find($id);
        $subscription_exit = Subscription::where('user_id', $id)->first();
        if (!$subscription_exit) {
//            dd('Condtion wrong');
            if ($plan_id == 1) {
                try {
                    $get = $user->newSubscription('RoadSide', 'plan_FEsvVtxkrrP14v')->create($token);
//                    $user->newSubscription('RoadSide', 'plan_FEsvVtxkrrP14v')->create($token);
//                    dd($get->stripe_id);
                    $id = Auth::user()->id;
                    $subscription_id = $get->stripe_id;

                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    $stripe_details = \Stripe\Subscription::retrieve($subscription_id);
                    $plan_amount = $stripe_details->plan->amount;
                    $timestamp = $stripe_details->current_period_end;
                    $periods_end = date('Y-m-d H:i:s', $timestamp);
                    $subscription = Subscription::where('user_id', $id)->where('stripe_id', $subscription_id)->first();
                    $subscription->ends_at = $periods_end;
                    $subscription->status = 1;
                    $subscription->save();


                    $stripe_details = \Stripe\Subscription::retrieve($subscription_id);
//                   $plan_name = $stripe_details->plan->nickname;
                    $plan_amount = $stripe_details->plan->amount;

                    $payement = new Payment();
                    $payement->user_id = $id;
                    $payement->amount = $plan_amount;
                    $payement->charge_id = $subscription_id;
                    $payement->save();

                    return sendSuccess('Subscription Create Successfully!', null);
                } catch (Exception $e) {
                    echo 'Message: ' . $e->getMessage();
                } catch (\Stripe\Error\Base $e) {
                    echo($e->getMessage());
                }
            } else if ($plan_id == 2) {
                try {
                    $get = $user->newSubscription('RoadSide', 'plan_FEsxkqVubAiUZz')->create($token);

                    $id = Auth::user()->id;
                    $subscription_id = $get->stripe_id;

                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    $stripe_details = \Stripe\Subscription::retrieve($subscription_id);
                    $plan_amount = $stripe_details->plan->amount;
                    $timestamp = $stripe_details->current_period_end;
                    $periods_end = date('Y-m-d H:i:s', $timestamp);

                    $subscription = Subscription::where('user_id', $id)->where('stripe_id', $subscription_id)->first();
                    $subscription->ends_at = $periods_end;
                    $subscription->status = 1;
                    $subscription->save();


                    $stripe_details = \Stripe\Subscription::retrieve($subscription_id);
                    $plan_amount = $stripe_details->plan->amount;

                    $payement = new Payment();
                    $payement->user_id = $id;
                    $payement->amount = $plan_amount;
                    $payement->charge_id = $subscription_id;
                    $payement->save();

                    return sendSuccess('Subscription Create Successfully!', null);
                } catch (Exception $e) {
                    echo 'Message: ' . $e->getMessage();
                } catch (\Stripe\Error\Base $e) {
                    echo($e->getMessage());
                }
            } else if ($plan_id == 3) {
                try {
                    $get = $user->newSubscription('RoadSide', 'plan_FEsyKIceDTMp7m')->create($token);

                    $id = Auth::user()->id;
                    $subscription_id = $get->stripe_id;

                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    $stripe_details = \Stripe\Subscription::retrieve($subscription_id);
                    $plan_amount = $stripe_details->plan->amount;
                    $timestamp = $stripe_details->current_period_end;
                    $periods_end = date('Y-m-d H:i:s', $timestamp);

                    $subscription = Subscription::where('user_id', $id)->where('stripe_id', $subscription_id)->first();
                    $subscription->ends_at = $periods_end;
                    $subscription->status = 1;
                    $subscription->save();


                    $stripe_details = \Stripe\Subscription::retrieve($subscription_id);
                    $plan_amount = $stripe_details->plan->amount;

                    $payement = new Payment();
                    $payement->user_id = $id;
                    $payement->amount = $plan_amount;
                    $payement->charge_id = $subscription_id;
                    $payement->save();

                    return sendSuccess('Subscription Create Successfully!', null);
                } catch (Exception $e) {
                    echo 'Message: ' . $e->getMessage();
                } catch (\Stripe\Error\Base $e) {
                    echo($e->getMessage());
                }
            } else if ($plan_id == 4) {
                try {
                    $get = $user->newSubscription('RoadSide', 'plan_FEsyKIceDTMp7m')->create($token);

                    $id = Auth::user()->id;
                    $subscription_id = $get->stripe_id;

                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    $stripe_details = \Stripe\Subscription::retrieve($subscription_id);
                    $plan_amount = $stripe_details->plan->amount;
                    $timestamp = $stripe_details->current_period_end;
                    $periods_end = date('Y-m-d H:i:s', $timestamp);

                    $subscription = Subscription::where('user_id', $id)->where('stripe_id', $subscription_id)->first();
                    $subscription->ends_at = $periods_end;
                    $subscription->status = 1;
                    $subscription->save();


                    $stripe_details = \Stripe\Subscription::retrieve($subscription_id);
                    $plan_amount = $stripe_details->plan->amount;

                    $payement = new Payment();
                    $payement->user_id = $id;
                    $payement->amount = $plan_amount;
                    $payement->charge_id = $subscription_id;
                    $payement->save();

                    return sendSuccess('Subscription Create Successfully!', null);
                } catch (Exception $e) {
                    echo 'Message: ' . $e->getMessage();
                } catch (\Stripe\Error\Base $e) {
                    echo($e->getMessage());
                }
            } else {
                return sendError('Invalid Plan_id', 405);
            }
        } else {
            return sendError('This user already have plan', 405);
        }
    }
    
     public function create_guest_service(Request $request) {
        $validator = Validator::make($request->all(), [
                    'token' => 'required',
                    'name' => 'required'
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages()->all();
            $messages = join("\n", $messages);
            return sendError($messages, 405);
        }

        $id = Auth::user()->id;
        $token = $request->token;
        $name = $request->name;
        $user = User::find($id);

         //name can be from these = {locksmith,   tire_change,   fuel_delivery,   tow,    jumpstart }

        if(!empty($name)){
            if ($name == 'tow') {
                
                $service = new Service();
                $service->user_id = $id;
                $service->name = $name;
                $service->save();
                
                return sendSuccess('Service Create Successfully!', null);
                
            } else {
                $amount = 89;
            
                try {
                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                    $charge = \Stripe\Charge::create([
                                'amount' => $amount,
                                'currency' => 'usd',
                                'description' => ucwords($name).' Service',
                                'source' => $token,
                    ]);
                    
                        $service = new Service();
                        $service->user_id = $id;
                        $service->name = $name;
                        $service->amount = $charge->amount;
                        $service->save();

                        $payement = new Payment();
                        $payement->user_id = $id;
                        $payement->amount = $charge->amount;
                        $payement->charge_id = $charge->id;
                        $payement->save();


                        return sendSuccess('Service Create Successfully!', null);

                    } catch (Exception $e) {
                        echo 'Message: ' . $e->getMessage();
                    } catch (\Stripe\Error\Base $e) {
                        echo($e->getMessage());
                    }
                }
            }  else {
                return sendError('Invalid Plan_id', 405);
            }
    }
    
    public function retrieve_job_id(Request $request) {
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'job_id' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages()->all();
            $messages = join("\n", $messages);
            return sendError($messages, 405);
        }

        $id = Auth::user()->id;
        $name = $request->name;
        $job_id = Jobs::where('job_id',$request->job_id)->first();
        $user = User::find($id);

         //name = {locksmith,   tire_change,   fuel_delivery,   tow,    jumpstart }

        if(!empty($name)){
                $service = Service::where('user_id',$id)->where('job_id',null)->where('name',$name)->where('status',1)->first();
                $service->job_id = $job_id->id;
                $service->save();
                return sendSuccess('Job Id Assigned Successfully!', null);
  
        }  else {
            return sendError('Invalid Name', 405);
        }
    }
    
    public function retrieve_miles_for_members(Request $request) {
        $validator = Validator::make($request->all(), [
                    'miles' => 'required',
                    'job_id' => 'required',
                    'is_allowed' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages()->all();
            $messages = join("\n", $messages);
            return sendError($messages, 405);
        }
        
        
        $id = Auth::user()->id;
        $is_allowed = $request->is_allowed;
        $miles = (double)$request->miles;
        $user = User::find($id);
        $job = Jobs::where('job_id',$request->job_id)->first();
        $sub_id = Subscription::find($user->getSubscription->id);
        $miles_allowed = $sub_id->total_miles;
//        
//        if(!empty($user->getSubscription) && $userdetail->getSubscription->counter == 0){
//            $message = 'You have 0 trips remaining.';
//            $data['miles_exceeded'] = false;
//            return sendSuccess($message, $data);
//        }
        
        if(!empty($miles)){
                if($miles > $miles_allowed){
                    $amount = (($miles-$miles_allowed)*5);
                }
                if($is_allowed == 0 && $miles > $miles_allowed) {
                    $message = ' Miles exceeded from your Membership Plan!. Do you want to continue with $5 per mile for extra miles?';
                    $data['miles_exceeded'] = true;
                    $data['message'] = ($miles-$miles_allowed).$message;
                    return sendSuccess($message, $data);
                
                } else if($is_allowed == 0 && $miles <= $miles_allowed) {
                    
                    Subscription::where('id', $user->getSubscription->id)->update(['counter' => $user->getSubscription->counter - 1]);
                    $service = Service::where('user_id',$id)->where('job_id',$job->id)->where('sub_id',$user->getSubscription->id)->where('name','tow')->first();
                    $service->miles_covered = $miles;
                    $service->status = 1;
                    $service->save();
                    
                    $message = 'You have successfully availed your service through membership.';
                    $data['miles_exceeded'] = false;
                    $data['message'] = $message;
                    return sendSuccess($message, $data);
                    
                } else if($is_allowed == 1) {
                    
                    $service = Service::where('user_id',$id)->where('job_id',$job->id)->where('sub_id',$user->getSubscription->id)->where('name','tow')->first();
                    if(!empty($service) && $service->status != 1){
                    
                        try {
                            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                            $charge = \Stripe\Charge::create([
                                'amount' => $amount*100,
                                'currency' => 'usd',
                                'description' => 'Tow Service Extended Membership Service',
                                'customer' => $user->stripe_id,
                            ]);

                            Subscription::where('id', $user->getSubscription->id)->update(['counter' => $user->getSubscription->counter - 1]);

                            $service->miles_covered = $miles;
                            $service->amount = $amount;
                            $service->status = 1;
                            $service->save();

                            $payement = new Payment();
                            $payement->user_id = $id;
                            $payement->amount = $charge->amount;
                            $payement->charge_id = $charge->id;
                            $payement->save();

                            $message = 'Members Tow Miles Added with amount Successfully!';
                            $data['miles_exceeded'] = false;
                            $data['message'] = $message;
                            return sendSuccess($message, $data);

                        } catch (Exception $e) {
                            echo 'Message: ' . $e->getMessage();
                        } catch (\Stripe\Error\Base $e) {
                            echo($e->getMessage());
                        }
                            
                    } 
//                    else {
//                        $message = 'Members Tow Miles Added with amount Successfully!';
//                        $data['miles_exceeded'] = 2;
//                        $data['message'] = $message;
//                        return sendSuccess($message, $data);
//                    }
                        
                } else if($is_allowed == 2) {
                    Service::where('user_id',$id)->where('job_id',$job->id)->where('name','tow')->delete();
//                    Subscription::where('id', $user->getSubscription->id)->update(['counter' => $user->getSubscription->counter + 1]);
                    $message = 'You opt to not use this service at the moment.';
                    $data['miles_exceeded'] = 2;
                    $data['message'] = $message;
                    return sendSuccess($message, $data);
                }
                
            }  else {
                return sendError('Invalid Service Type', 405);
            }
    }
    
        
    public function pay_per_use_member(Request $request) {
        $validator = Validator::make($request->all(), [
                    'miles' => 'required',
                    'name' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages()->all();
            $messages = join("\n", $messages);
            return sendError($messages, 405);
        }
        
        
        $id = Auth::user()->id;
        $miles = (double)$request->miles;
        $user = User::find($id);
        $sub_id = Subscription::find($user->getSubscription->id);
        $miles_allowed = $sub_id->total_miles;
        $name = $request->name;
                
        if ($name == 'tow'){
            $amount = 99;
        } else {
            $amount = 89;
        }
        
        if($miles > 0){
                $amount = $amount + (($miles)*5);
        } 
        
        if(!empty($sub_id) && $sub_id->status == 1){

            try {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                $charge = \Stripe\Charge::create([
                    'amount' => $amount*100,
                    'currency' => 'usd',
                    'description' => $name.' Service Pay Per Use',
                    'customer' => $user->stripe_id,
                ]);

                $payement = new Payment();
                $payement->user_id = $id;
                $payement->amount = $charge->amount;
                $payement->charge_id = $charge->id;
                $payement->save();

                $message = 'Members Service added with amount Successfully!';
                
                $data['message'] = $message;
                return sendSuccess($message, $data);

            } catch (Exception $e) {
                echo 'Message: ' . $e->getMessage();
            } catch (\Stripe\Error\Base $e) {
                echo($e->getMessage());
            }

        } 
             
    }
    
    
    public function retrieve_miles_for_service(Request $request) {
        $validator = Validator::make($request->all(), [
                    'token' => 'required',
                    'name' => 'required',
                    'miles' => 'required',
                    'job_id' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages()->all();
            $messages = join("\n", $messages);
            return sendError($messages, 405);
        }

        $id = Auth::user()->id;
        $token = $request->token;
        $name = $request->name;
        $miles = (double)$request->miles;
        $user = User::find($id);
        $job = Jobs::where('job_id',$request->job_id)->first();

         //name = {locksmith,   tire_change,   fuel_delivery,   tow,    jumpstart }

        if(!empty($name)){
            if ($name == 'tow') {
                $amount = 99;
                if($miles > 0){
                    $amount = $amount + ($miles*5);
                } 
                try {
                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                    $charge = \Stripe\Charge::create([
                                'amount' => $amount*100,
                                'currency' => 'usd',
                                'description' => ucwords($name).' Service',
                                'source' => $token,
                    ]);
                    
                        $service = Service::where('user_id',$id)->where('job_id',$job->id)->where('name',$name)->first();
                        $service->miles_covered = $miles;
                        $service->amount = $amount;
                        $service->status = 1;
                        $service->save();

                        $payement = new Payment();
                        $payement->user_id = $id;
                        $payement->amount = $charge->amount;
                        $payement->charge_id = $charge->id;
                        $payement->save();


                        return sendSuccess('Miles Added with amount Successfully!', null);

                    } catch (Exception $e) {
                        echo 'Message: ' . $e->getMessage();
                    } catch (\Stripe\Error\Base $e) {
                        echo($e->getMessage());
                    }
                    
            } else {
                return sendError('Invalid Service Type', 405);
            }
                
                
            }  else {
                return sendError('Invalid Service Type', 405);
            }
    }

    public function get_subscription_plan() {

        $id = Auth::user()->id;

        $stripe_id = Subscription::where('user_id', $id)->first();
        if ($stripe_id) {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $signle_plan['subscription'] = \Stripe\Subscription::retrieve($stripe_id->stripe_id);

            return sendSuccess('Subscription Plan', $signle_plan);
        } else {
            return sendError('No subscription plan', null);
        }
    }

    public function cancel_subscription() {
        $id = Auth::user()->id;
        $user = User::find($id);
        $stripe_id = Subscription::where('user_id', $id)->where('status', '0')->first();

        if ($stripe_id) {

            try {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                $sub = \Stripe\Subscription::retrieve($stripe_id->stripe_id);
                $sub->cancel();
//              $user->subscription('main')->cancel();
                //$stripe_id->delete();
//                $stripe_id = $stripe_id->stripe_id;
//                $subscription = Subscription::where('user_id',$id)->where('stripe_id',$stripe_id)->first();
                $stripe_id->status = 0;
                $stripe_id->save();
                return sendSuccess('Subscription cancel successfully!!!', null);
            } catch (Exception $e) {
                echo 'Message: ' . $e->getMessage();
            } catch (\Stripe\Error\Base $e) {
                echo($e->getMessage());
            }
        } else {
            return sendError('No subscription plan', 405);
        }
    }

    public function create_charge(Request $request) {
        $validator = Validator::make($request->all(), [
                    'token' => 'required',
                    'amount' => 'required'
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages()->all();
            $messages = join("\n", $messages);
            return sendError($messages, 405);
        }
        $token = $request->token;
        $amount = $request->amount;

        $id = Auth::user()->id;
        $subscription = Subscription::where('user_id', $id)->first();
        if ($subscription) {
            return sendError('Already have subscription', 405);
        } else {
            try {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                $charge = \Stripe\Charge::create([
                            'amount' => $amount,
                            'currency' => 'usd',
                            'description' => 'Test charge',
                            'source' => $token,
                ]);

                $payement = new Payment();
                $payement->user_id = $id;
                $payement->amount = $charge->amount;
                $payement->charge_id = $charge->id;
                $payement->save();
                return sendSuccess('Charge succesfully!!!', $charge);
            } catch (Exception $e) {
                echo 'Message: ' . $e->getMessage();
            } catch (\Stripe\Error\Base $e) {
                echo($e->getMessage());
            }
        }
    }

    function adminCancelSub(Request $request) {

        $id = $request->id;
        $user = User::find($id);
        $stripe_id = Subscription::where('user_id', $id)->where('status', '1')->first();

        if ($stripe_id) {

            try {

                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $sub = \Stripe\Subscription::retrieve($stripe_id->stripe_id);
                $sub->cancel();
//              $user->subscription('main')->cancel();
//                $stripe_id->delete();
                //$stripe_id = $stripe_id->stripe_id;
                //$subscription = Subscription::where('user_id',$id)->where('stripe_id',$stripe_id)->first();
                $stripe_id->status = 0;
                $stripe_id->save();
                return 1;
            } catch (Exception $e) {
                echo 'Message: ' . $e->getMessage();
            } catch (\Stripe\Error\Base $e) {
                echo($e->getMessage());
            }
        } else {
            return sendError('No subscription plan', 405);
        }
    }

    function userDetail($id) {


        $data['tab'] = '';
        $data['title'] = 'Details';
        $data['user'] = User::with('getSubscription', 'getPaymnet')->where('id', $id)->orderBy('id', 'DESC')->get();

//        dd($data['user']);
        return view('admin.user-detail', $data);
    }

    function usedServices($id) {


        $data['tab'] = '';
        $data['title'] = 'Used Services';
        $data['user'] = User::with('getJob')->where('id', $id)->orderBy('id', 'DESC')->get();

//        dd($data['user']);
        return view('admin.used-services', $data);
    }

}
