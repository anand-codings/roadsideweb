<?php

namespace App\Http\Middleware;
use Closure;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\User;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headers = getallheaders();
        
        if(isset($headers['Authorization']))
        {
            $auth = explode(' ',$headers['Authorization']);
            $checksession = User::where('session_token', $auth[1])->first();
            if ($checksession) {
                // Auth::login($checksession);
                return $next($request);
            } else {
                return Response::json(array('status' => 'error', 'errorMessage' => 'Session Expired', 'errorCode' => 401), 401);
            }
        }
        else
        {
            return Response::json(array( 'status' => 'error', 'errorMessage' => 'Undefined session_token' ));
        }
       
    }
}
