<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use App\Cars;
use App\Jobs;
use App\Subscription;
use App\Service;
use Illuminate\Support\Facades\Auth; 
use Validator;

class JobsController extends Controller
{
    public $successStatus = 200;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'job_id' => 'required|string|unique:jobs', 
            'status' => 'required|string', 
            'lat' => 'string', 
            'lng' => 'string',
            'type' => 'string',  
        ]);
        if ($validator->fails()) { 
            return sendError($validator->errors(), 401);              
        }
        $user = Auth::user();
        $userdetail = User::find($user->id);
        
        if(!empty($userdetail->getSubscription) && $userdetail->getSubscription->counter == 0){
            return sendSuccess('You have 0 trips remaining.', null);
        }

        $input = $request->all(); 
        $input['user_id'] = $user->id;
        $job = Jobs::create($input);

         //name = {locksmith,   tire_change,   fuel_delivery,   tow,    jumpstart }

         if($request->type == 0){
             $name = "jumpstart";
         } else if($request->type == 1){
             $name = "locksmith";
         } else if($request->type == 2){
             $name = "tow";
         } else if($request->type == 3){
             $name = "tire_change";
         } else if($request->type == 4){
             $name = "fuel_delivery";
         }
         
         $job_id = Jobs::where('job_id',$request->job_id)->first();

         if(!empty($userdetail->getSubscription)){
            $service = new Service();
            $service->user_id = $userdetail->id;
            $service->name = $name;
            $service->job_id = $job_id->id;
            $service->sub_id = $userdetail->getSubscription->id;
//            if($name != 'tow'){
            $service->status = 1;
//            }
            $service->save();

            $subscription = Subscription::find($userdetail->getSubscription->id);
//            if($name != 'tow'){
            $subscription->counter = $userdetail->getSubscription->counter - 1;
//            }
            $subscription->save();

         } else {
            $service = Service::where('user_id',$user->id)->where('job_id', null)->where('name',$name)->first();
            $service->job_id = $job_id->id;
            if($name != 'tow'){
                $service->status = 1;
            }
            $service->save();
         }
        return sendSuccess('Job Created Successfully', $job);
        
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $job = Jobs::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if (!$job)
           return sendError('No such job exists', 401);

        return sendSuccess('Job exists', $job);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $job = Jobs::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if (!$job)
            return sendError('No such job exists', 401);

        return sendSuccess('Edit Job', $job);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'id' => 'required|exists:jobs',
        ]);
        if ($validator->fails()) { 
            return sendError($validator->errors(), 401);              
        }

        $job = Jobs::where('id', $request->id)->where('user_id', Auth::user()->id)->first();
        if (!$job)
            return sendError('No such job exists', 401);

        $job->update($request->all());
        return sendSuccess('Job Successfully Updated', $job);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = Jobs::where('id', $id)->where('user_id', Auth::user()->id)->delete();
        if ($result) {
            return sendSuccess('Job Successfully Removed', $result);
        }
        
        return sendError('No such job exists', 401);
    }

    public function all_jobs()
    {
        $jobs = Jobs::where('user_id', Auth::user()->id)->get();
        if ($jobs->isEmpty())
            return sendError('No jobs added', 404);

        return sendSuccess('All Jobs', $jobs);
    }
    
    public function cancel_job(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'job_id' => 'required|string', 
        ]);
        if ($validator->fails()) { 
            return sendError($validator->errors(), 401);              
        }
        $user = Auth::user();
        $userdetail = User::find($user->id);
        $job_id = Jobs::where('job_id',$request->job_id)->first();
        $service = Service::where('user_id',$user->id)->where('job_id', $job_id->id)->delete();
        $subscription = Subscription::find($userdetail->getSubscription->id);
        $subscription->counter = $userdetail->getSubscription->counter +1;
        $subscription->save();
        
        return sendSuccess('Job Canceled Successfull', $job_id);
        
    }
}
