<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\User;
use App\PasswordReset;
use Validator;
use Illuminate\Support\Facades\Mail;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;
/*
    function reset(Request $request) {
        $request->validate([
            'password' => 'required|confirmed',
            'token' => 'required'
        ]);
        $passwordReset = PasswordReset::where('token', $request->token)->first();
        if (!$passwordReset)
            return sendError('This password reset token is invalid.', 404);
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user)
            return sendError('We cannot find a user with that e-mail address.', 404);
        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        return redirect('/success');
    }
*/
     function reset(Request $request) {
        
        $request->validate([
            'password' => 'required|confirmed|min:6|max:191',
            'password_confirmation' => 'required|max:191',
            'token' => 'required|string'
        ]);
        $passwordReset = PasswordReset::where('token', $request->token)->first();
        if (!$passwordReset)
            return view('reset_password_link_expired');
//            return sendError('This password reset token is invalid.', 404);
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user)
            return view('reset_password_link_expired');
//            return sendError('We cannot find a user with that e-mail address.', 404);
        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
//        return sendSuccess('Password Successfully Changed!', $user);
        return view('reset_success');
    }
}
