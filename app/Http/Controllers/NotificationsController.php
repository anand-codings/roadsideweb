<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OneSignal;
use View;
use App\Reminders;

class NotificationsController extends Controller {

    public function cron() {
        $jobs = DB::table('jobs')->whereNotIn('status', ['completed', 'canceled', 'goa','released ', 'reassigned'])->get();
        $client = new \GuzzleHttp\Client();
        foreach ($jobs as $job) {
            $status = $job->status;
            $res = $client->request('POST', 'https://staging.joinswoop.com/graphql', ['headers' => ['Authorization' => 'Bearer AKgQquPL5ws-TcEF_jdr7L20pACQ3Kv3qPz8t4aBZhU'],
                'form_params' => ['query' => 'query getStatus {
                          job(id: "' . $job->job_id . '") {
                            id
                            swcid
                            createdAt
                            status
                            partner {
                              name
                              phone
                              driver {
                                name
                                phone
                              }
                              vehicle {
                                location {
                                  lat
                                  lng
                                }
                              }
                            }
                            eta {
                              current
                            }
                          }
                        }']]);

            $json = json_decode($res->getBody());
            if (isset($json->data->job) && $status != strtolower($json->data->job->status) && $json->data->job->status != 'Canceled') {
                 $dataToSend['status'] = $json->data->job->status;
                if (strtolower($json->data->job->status) == 'accepted') {
                    $dataToSend['statuss'] = "Rescue Accepted";
                } else if (strtolower($json->data->job->status) == 'assigned') {
                    $dataToSend['statuss'] = "Pending";
                } else if ($job->type == 2 && strtolower($json->data->job->status) == 'towdestination') {
                    $dataToSend['statuss'] = "Dropping Off";
                } else if ($job->type != 2 && (strtolower($json->data->job->status) == 'towing' || strtolower($json->data->job->status) == 'towdestination' )) {
                    $dataToSend['statuss'] = "In Progress";
                } else if (strtolower($json->data->job->status) == 'enroute') {
                    $dataToSend['statuss'] = "En Route";
                } else {
                    $dataToSend['statuss'] = $json->data->job->status;
                }
                $dataToSend['job_id'] = $job->job_id;
                OneSignal::sendNotificationUsingTags(
                        "Status : " . $dataToSend['statuss'], array(
                    ["field" => "tag", "key" => 'user_id', "relation" => "=", "value" => $job->user_id],
                        ), $url = null, $data = $dataToSend, $buttons = null, $schedule = null
                );


                DB::table('jobs')->where('job_id', $job->job_id)->update(['status' => strtolower($json->data->job->status)]);
            }
        }
    }

    public function getLocation($job_id) {
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', 'https://staging.joinswoop.com/graphql', ['headers' => ['Authorization' => 'Bearer AKgQquPL5ws-TcEF_jdr7L20pACQ3Kv3qPz8t4aBZhU'],
            'form_params' => ['query' => 'query {
                            job(id: "' . $job_id . '") {
                              partner {
                                vehicle {
                                    location {
                                    lat
                                    lng
                                  }
                                }
                              }
                            }
                          }']]);

        $location = json_decode($res->getBody());
        if ($location->data->job->partner->vehicle) {
            view('location', ['job_id' => $job_id, 'lat' => $location->data->job->partner->vehicle->location->lat, 'lng' => $location->data->job->partner->vehicle->location->lng])->render();
        } else {
            return view('location', ['job_id' => $job_id, 'lat' => '0.00', 'lng' => '0.00'])->render();
        }
    }

    public function sendReminders() {
        $reminders = DB::table('reminders')->where('all_sent', 0)->get();
        $date_today = date('Y-m-d');
        $time_today = strtotime(date('H:i:s'));

        foreach ($reminders as $reminder) {
            $update_reminders = Reminders::find($reminder->id);
            if (strpos($update_reminders->time_zone, '+') !== false) {
                $bool = true;
            } else {
                $bool = false;
            }
            $add = substr($update_reminders->time_zone, 1);
            if ($reminder->purchase_sent == 0 && $reminder->purchase_date != null) {
                if ($bool) {
                    if ($reminder->purchase_date == $date_today && (strtotime($reminder->purchase_time) - ($add * 3600)) <= $time_today) {
                        $dataToSend['description'] = $reminder->purchase_description;
                        OneSignal::sendNotificationUsingTags(
                                $reminder->purchase_description, array(
                            ["field" => "tag", "key" => 'user_id', "relation" => "=", "value" => $reminder->user_id],
                                ), $url = null, $data = $dataToSend, $buttons = null, $schedule = null, ucfirst($reminder->title)
                        );
                        $update_reminders->purchase_sent = 1;
                    }
                } else {
                    if ($reminder->purchase_date == $date_today && (strtotime($reminder->purchase_time) + ($add * 3600)) <= $time_today) {
                        $dataToSend['description'] = $reminder->purchase_description;
                        OneSignal::sendNotificationUsingTags(
                                $reminder->purchase_description, array(
                            ["field" => "tag", "key" => 'user_id', "relation" => "=", "value" => $reminder->user_id],
                                ), $url = null, $data = $dataToSend, $buttons = null, $schedule = null, ucfirst($reminder->title)
                        );
                        $update_reminders->purchase_sent = 1;
                    }
                }
            }
            if ($reminder->insurance_sent == 0 && $reminder->insurance_date != null) {
                if ($bool) {
                    if ($reminder->insurance_date == $date_today && (strtotime($reminder->insurance_time) - ($add * 3600)) <= $time_today) {
                        $dataToSend['description'] = $reminder->insurance_description;
                        OneSignal::sendNotificationUsingTags(
                                $reminder->insurance_description, array(
                            ["field" => "tag", "key" => 'user_id', "relation" => "=", "value" => $reminder->user_id],
                                ), $url = null, $data = $dataToSend, $buttons = null, $schedule = null, ucfirst($reminder->title)
                        );
                        $update_reminders->insurance_sent = 1;
                    }
                } else {
                    if ($reminder->insurance_date == $date_today && (strtotime($reminder->insurance_time) + ($add * 3600)) <= $time_today) {
                        $dataToSend['description'] = $reminder->insurance_description;
                        OneSignal::sendNotificationUsingTags(
                                $reminder->insurance_description, array(
                            ["field" => "tag", "key" => 'user_id', "relation" => "=", "value" => $reminder->user_id],
                                ), $url = null, $data = $dataToSend, $buttons = null, $schedule = null, ucfirst($reminder->title)
                        );
                        $update_reminders->insurance_sent = 1;
                    }
                }
            }
            if ($reminder->maintainence_sent == 0 && $reminder->maintainence_date != null) {
                if ($bool) {
                    if ($reminder->maintainence_date == $date_today && (strtotime($reminder->maintainence_time) - ($add * 3600)) <= $time_today) {
                        $dataToSend['description'] = $reminder->maintainence_description;
                        OneSignal::sendNotificationUsingTags(
                                $reminder->maintainence_description, array(
                            ["field" => "tag", "key" => 'user_id', "relation" => "=", "value" => $reminder->user_id],
                                ), $url = null, $data = $dataToSend, $buttons = null, $schedule = null, ucfirst($reminder->title)
                        );
                        $update_reminders->maintainence_sent = 1;
                    }
                } else {
                    if ($reminder->maintainence_date == $date_today && (strtotime($reminder->maintainence_time) + ($add * 3600)) <= $time_today) {
                        $dataToSend['description'] = $reminder->maintainence_description;
                        OneSignal::sendNotificationUsingTags(
                                $reminder->maintainence_description, array(
                            ["field" => "tag", "key" => 'user_id', "relation" => "=", "value" => $reminder->user_id],
                                ), $url = null, $data = $dataToSend, $buttons = null, $schedule = null, ucfirst($reminder->title)
                        );
                        $update_reminders->maintainence_sent = 1;
                    }
                }
            }

            if ($update_reminders->maintainence_sent == 1 && $update_reminders->purchase_sent == 1 && $update_reminders->insurance_sent == 1) {
                $update_reminders->all_sent = 1;
            }
            $update_reminders->save();
        }
    }

    public function getStatus($job_id) {
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', 'https://staging.joinswoop.com/graphql', ['headers' => ['Authorization' => 'Bearer AKgQquPL5ws-TcEF_jdr7L20pACQ3Kv3qPz8t4aBZhU'],
            'form_params' => ['query' => 'query getStatus {
                          job(id: "' . $job_id . '") {
                            id
                            swcid
                            createdAt
                            status
                            partner {
                              name
                              phone
                              driver {
                                name
                                phone
                              }
                              vehicle {
                                location {
                                  lat
                                  lng
                                }
                              }
                            }
                            eta {
                              current
                            }
                          }
                        }']]);

        $json = json_decode($res->getBody());
        if (isset($json->data->job) && isset($json->data->job->status)) {
            $status = $json->data->job->status;
            return sendSuccess('Status', $status);
        }
        return sendError('No such job', 404);
    }

}
