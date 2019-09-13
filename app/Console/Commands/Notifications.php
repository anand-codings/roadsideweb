<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use OneSignal;

class Notifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Notification:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifications for status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $jobs = DB::table('jobs')->where('status', '=', 'pending')->get();
        $jobs = DB::table('jobs')->whereNotIn('status', ['completed', 'canceled', 'goa'])->get();
        $client = new \GuzzleHttp\Client();
        foreach ($jobs as $job) {
          $status = $job->status;
            $res = $client->request('POST','https://staging.joinswoop.com/graphql', ['headers' => ['Authorization' => 'Bearer 220d13b145b0bb27d0edeeb2324e1a81c3136340257b1f111dfa334f846b0d21'],
                'form_params'=> ['query' => 'query getStatus {
                          job(id: "'.$job->job_id.'") {
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
            if ($status != strtolower($json->data->job->status)) {
              OneSignal::sendNotificationUsingTags(
              "hello testing", array(
                  ["field" => "user_id", "relation" => ">", "value" => $job->user_id],
                      ), $url = null, $data = strtolower($json->data->job->status), $buttons = null, $schedule = null
              );

              DB::table('jobs')->where('job_id', $job->job_id)->update(['status' => strtolower($json->data->job->status)]);
            }

            \Log::info($res->getBody());
        }
    }
}
