<?php

use Illuminate\Support\Facades\Response;

function sendSuccess($message, $data) {
//    return Response::json(array('status' => 'success', 'successMessage' => $message, 'successData' => $data), 200, [], JSON_NUMERIC_CHECK);
    return Response::json(array('status' => 'success', 'successMessage' => $message, 'successData' => $data), 200, []);
}

function sendError($error_message, $code) {
    return Response::json(array('status' => 'error', 'errorMessage' => $error_message), $code);
}

function getVideoInformation($video_information) {
    $regex_duration = "/Duration: ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}).([0-9]{1,2})/";
    if (preg_match($regex_duration, implode(" ", $video_information), $regs)) {
        $hours = $regs [1] ? $regs [1] : null;
        $mins = $regs [2] ? $regs [2] : null;
        $secs = $regs [3] ? $regs [3] : null;
        $ms = $regs [4] ? $regs [4] : null;
        $random_duration = sprintf("%02d:%02d:%02d", rand(0, $hours), rand(0, $mins), rand(0, $secs));
        $original_duration = $hours . ":" . $mins . ":" . $secs;
        $parsed = date_parse($original_duration);
        $seconds = ($parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second']) > 20 ? true : false;
        return [$original_duration, $random_duration, $seconds];
    }
}

function subscribed_user($stripe_id) {
    $user = \App\Subscription::where('stripe_id', $stripe_id)->with('getUser')->first();
    if ($user) {
        return $user->getUser->name;
    } else {
        return false;
    }
}
function subscribed_mile_coverd($stripe_id) {
    $miles = \App\Subscription::where('stripe_id', $stripe_id)->first();
    if ($miles) {
        return $miles;
    } else {
        return false;
    }
}

function get_user_charge($user_id) {
    try {
        $charge = \App\Payment::where('user_id', $user_id)->first();

        return $charge;
    } catch (Stripe_CardError $e) {
        $e->getMessage();
        return false;
    } catch (Stripe_InvalidRequestError $e) {
        $e->getMessage();
        return false;
    } catch (Stripe_AuthenticationError $e) {
        $e->getMessage();
        return false;
    } catch (Stripe_ApiConnectionError $e) {
        $e->getMessage();
        return false;
    } catch (Stripe_Error $e) {
        $e->getMessage();
        return false;
    } catch (Exception $e) {
        $e->getMessage();
        return false;
    }
}

function get_subscription($user_id) {
    $subccription = \App\Subscription::where('user_id', $user_id)->first();
    if ($subccription) {
        try {
            \Stripe\Stripe::setApiKey('sk_test_5U2DWCQfgd0issmQbyH3MSOi');

            $signle_plan = \Stripe\Subscription::retrieve($subccription->stripe_id);
            return $signle_plan;
        } catch (Stripe_CardError $e) {
            $e->getMessage();
            return false;
        } catch (Stripe_InvalidRequestError $e) {
            $e->getMessage();
            return false;
        } catch (Stripe_AuthenticationError $e) {
            $e->getMessage();
            return false;
        } catch (Stripe_ApiConnectionError $e) {
            $e->getMessage();
            return false;
        } catch (Stripe_Error $e) {
            $e->getMessage();
            return false;
        } catch (Exception $e) {
            $e->getMessage();
            return false;
        }
    } else {
        return false;
    }
}
