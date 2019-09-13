<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Reminders;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Validator;

class RemindersController extends Controller {

    public $successStatus = 200;

    public function index() {
        $reminders = Reminders::where('user_id', Auth::user()->id)->get();
        return sendSuccess('All Reminders', $reminders);
    }

    public function addReminder(Request $request) {
        $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'time_zone' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError("Time Zone and Title are Required", 401);
        }

        if ($request['insurance_date'] && $request['insurance_time']) {
            \Illuminate\Support\Facades\Log::info('yahan');
            $time = $request['insurance_date'] . ' ' . $request['insurance_time'];
            $time = new DateTime($time);
            $time = $time->format('Y-m-d H:i:s');
            $utcTime = new DateTime($time, new DateTimeZone($request['time_zone']));
            $utcTime->setTimeZone(new DateTimeZone('UTC'));
//            $utcTime = $utcTime->format('Y-m-d H:i:s');
            if ($utcTime < Carbon::now()) {
                return sendError("Insurance time cannot be less than current time.", 401);
            }
        }

        if ($request['purchase_date'] && $request['purchase_time']) {
            $time = $request['purchase_date'] . ' ' . $request['purchase_time'];
            $time = new DateTime($time);
            $time = $time->format('Y-m-d H:i:s');
            $utcTime = new DateTime($time, new DateTimeZone($request['time_zone']));
            $utcTime->setTimeZone(new DateTimeZone('UTC'));
//            $utcTime = $utcTime->format('Y-m-d H:i:s');
            if ($utcTime < Carbon::now()) {
                return sendError("Purchase time cannot be less than current time.", 401);
            }
        }

        if ($request['maintainence_date'] && $request['maintainence_time']) {
            $time = $request['maintainence_date'] . ' ' . $request['maintainence_time'];
            $time = new DateTime($time);
            $time = $time->format('Y-m-d H:i:s');
            $utcTime = new DateTime($time, new DateTimeZone($request['time_zone']));
            $utcTime->setTimeZone(new DateTimeZone('UTC'));
//            $utcTime = $utcTime->format('Y-m-d H:i:s');
            if ($utcTime < Carbon::now()) {
                return sendError("Maintenance time cannot be less than current time.", 401);
            }
        }

        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $reminder = Reminders::create($input);
        return sendSuccess('Reminder Added Successfully', $reminder);
    }

    public function showReminder(Request $request) {
        $validator = Validator::make($request->all(), [
                    'reminder_id' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError($validator->errors(), 401);
        }

        $reminder = Reminders::where('user_id', Auth::user()->id)->where('id', $request->reminder_id)->where('all_sent', 0)->first();
        return sendSuccess('Reminder', $reminder);
    }

    public function updateReminder(Request $request) {
        $validator = Validator::make($request->all(), [
                    'id' => 'required|exists:reminders',
                    'title' => 'required',
                    'time_zone' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError('Time Zone, Reminder ID and Title are Required', 401);
        }

        $reminder = Reminders::where('id', $request->id)->where('user_id', Auth::user()->id)->first();
        if (!$reminder)
            return sendError('No such Reminder exists', 401);
        
        if ($request['insurance_date'] && $request['insurance_time']) {
            $time = $request['insurance_date'] . ' ' . $request['insurance_time'];
            $time = new DateTime($time);
            $time = $time->format('Y-m-d H:i:s');
            $utcTime = new DateTime($time, new DateTimeZone($request['time_zone']));
            $utcTime->setTimeZone(new DateTimeZone('UTC'));
//            $utcTime = $utcTime->format('Y-m-d H:i:s');
            if ($utcTime < Carbon::now()) {
                return sendError("Insurance time cannot be less than current time.", 401);
            }
        }

        if ($request['purchase_date'] && $request['purchase_time']) {
            $time = $request['purchase_date'] . ' ' . $request['purchase_time'];
            $time = new DateTime($time);
            $time = $time->format('Y-m-d H:i:s');
            $utcTime = new DateTime($time, new DateTimeZone($request['time_zone']));
            $utcTime->setTimeZone(new DateTimeZone('UTC'));
//            $utcTime = $utcTime->format('Y-m-d H:i:s');
            if ($utcTime < Carbon::now()) {
                return sendError("Purchase time cannot be less than current time.", 401);
            }
        }

        if ($request['maintainence_date'] && $request['maintainence_time']) {
            $time = $request['maintainence_date'] . ' ' . $request['maintainence_time'];
            $time = new DateTime($time);
            $time = $time->format('Y-m-d H:i:s');
            $utcTime = new DateTime($time, new DateTimeZone($request['time_zone']));
            $utcTime->setTimeZone(new DateTimeZone('UTC'));
//            $utcTime = $utcTime->format('Y-m-d H:i:s');
            if ($utcTime < Carbon::now()) {
                return sendError("Maintenance time cannot be less than current time.", 401);
            }
        }

        if (isset($request->purchase_date) && isset($request->purchase_time)) {
            $request['purchase_sent'] = 0;
        }
        if (isset($request->insurance_date) && isset($request->insurance_time)) {
            $request['insurance_sent'] = 0;
        }
        if (isset($request->maintainence_date) && isset($request->maintainence_time)) {
            $request['maintainence_sent'] = 0;
        }
        $request['all_sent'] = 0;
        $reminder->update($request->all());
        return sendSuccess('Reminder Successfully Updated', $reminder);
    }

    public function deleteReminder($id) {
        $result = Reminders::where('id', $id)->where('user_id', Auth::user()->id)->delete();
        if ($result) {
            return sendSuccess('Reminder Successfully Removed', $result);
        }

        return sendError('No such reminder exists', 401);
    }

}
