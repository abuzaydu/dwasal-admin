<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\NotificationType;
use App\Models\SmsTemplate;
use App\Models\User;
use Log;

class EventInviteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $memderids;
    protected $event;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($memderids, $event)
    {
        $this->memderids = $memderids;
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $ntype = NotificationType::where('type', 'Event Invitation')->first();
        // if (!is_null($ntype)) {
        //     $template = SmsTemplate::where('notification_type_id', $ntype->id)->first();
        //     if (!is_null($template)) {
        //         $users = array();
        //         foreach ($this->memderids as $id) {
        //             $user = User::where('id', $id)->select('name', 'phone', 'email')->first();
        //             array_push($users, ['name' => $user->name, 'phone' => $user->phone, 'email' => $user->email]);
        //         }
        //         $usrArr = $this->my_array_unique($users);
        //         // Log::info($usrArr);
        //         foreach ($usrArr as $usr) {
        //             $message = $this->modifyMessage($this->event, $template->content, $usr);
        //             Log::info($message);
        //             // $this->sendSMS($usr['phone'], $message);
        //         }
        //     }else{
        //         Log::info('No SMS Template for Event Invitation notification');
        //     }
        // }
    }

    public function modifyMessage($event, $template, $user)
    {
        $msg = str_replace('[full_name]', $user['name'], $template);
        $msg = str_replace('[start_time]', date('d M Y H:i A', strtotime($event->start)), $msg);
        $msg = str_replace('[location]', $event->location, $msg);

        return $msg;
    }

    public function sendSMS($phone, $message)
    {
        if (!is_null($this->formattedNumber($phone))) {
            $numbers = [$this->formattedNumber($phone)];
            $token = '8b49c1406246765709bfdbaa6b8a9232';
            $client = new \GuzzleHttp\Client();
            $url = "https://ovalbsms.co.tz/api/send-sms";
            $data = array(
                'form_params' => array(
                    'username' => 'NSTS',
                    'password' => 'nst@2023',
                    'sender' => 'NSTInfo',
                    'receiver' => $numbers,
                    'message' => $message,
                ),
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Accept' => 'application/json',
                ],
            );

            // $client->setDefaultOption(array('verify', false));
            $req = $client->post($url,  $data);
          
            $response = $req->getBody();

            // SmsResponseLog::create([
            //     'response' => $response
            // ]);
        }else{
            Log::info('Mobile Number '.$phone.' is invalid, SMS not sent.');
        }
    }

    public function formattedNumber($number)
    {
        if ($this->validate_mobile($number)) {
            $num = preg_replace('/^(?:\+?255|0)?/','255', $number);
            return $num;
        } else{
            return null;
        }
    }

    public function validate_mobile($mobile)
    {   
        $mobile = str_replace(' ', '', $mobile);
        $mobile = preg_replace('/^(?:\+?255|0)?/','0', $mobile);
        return preg_match('/^[0-9]{10}+$/', $mobile);
    }

    private function my_array_unique($array, $keep_key_assoc = false){
        $duplicate_keys = array();
        $tmp = array();       

        foreach ($array as $key => $val){
            // convert objects to arrays, in_array() does not support objects
            if (is_object($val))
                $val = (array)$val;

            if (!in_array($val, $tmp))
                $tmp[] = $val;
            else
                $duplicate_keys[] = $key;
        }

        foreach ($duplicate_keys as $key)
            unset($array[$key]);

        return $keep_key_assoc ? $array : array_values($array);
    }
}
