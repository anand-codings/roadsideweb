<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use App\Cars;
use Illuminate\Support\Facades\Auth; 
use Validator;
use DB;

class CarsController extends Controller
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
            'name' => 'required|string', 
            'make' => 'required|string', 
            'model' => 'required|string', 
            'year' => 'required|string', 
            'color' => 'required|string', 
//            'vin' => 'required|string',
        ]);
        if ($validator->fails()) { 
            return sendError($validator->errors(), 401);            
        }
        $user = Auth::user();
        $input = $request->all(); 
        $input['user_id'] = $user->id;  
        $car = Cars::create($input);
        return sendSuccess('Car Added Successfully', $car);
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
        $car = Cars::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if (!$car)
            return sendError('No such car exists.', 404);

        return sendSuccess('Car Detail', $car);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $car = Cars::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if (!$car)
            return sendError('No such car exists.', 404);

        return sendSuccess('Edit Car Detail', $car);
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
        $car = Cars::where('id', $request->id)->where('user_id', Auth::user()->id)->first();
        if (!$car)
            return sendError('No such car exists.', 404);

        $car->update($request->all());
        return sendSuccess('Car Updated Successfully', $car);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = Cars::where('id', $id)->where('user_id', Auth::user()->id)->delete();
        if ($result) {
            return sendSuccess('Car Removed Successfully', $result);
        }
        
        return sendError('No such car exists.', 404);
    }

    public function all_cars()
    {
        $cars = Cars::where('user_id', Auth::user()->id)->get();
        if ($cars->isEmpty())
            return sendError('No cars added.', 404);

        return sendSuccess('All Cars', $cars);
    }

    public function getMake(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'year' => 'required'
        ]);
        if ($validator->fails()) { 
            return sendError($validator->errors(), 401);            
        }

        $all_makes = DB::table('all_cars')->where('year', $request->year)->groupBy('make')->pluck('make');
        return sendSuccess('All Car Makes', $all_makes);
    }

    public function getModel(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'year' => 'required',
            'make' => 'required'
        ]);
        if ($validator->fails()) { 
            return sendError($validator->errors(), 401);            
        }

        $all_models = DB::table('all_cars')->where('year', $request->year)->where('make', $request->make)->pluck('model');
        return sendSuccess('All Car Models', $all_models);
    }

}
