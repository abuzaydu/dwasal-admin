<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SendSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    protected $token;
    protected $username;
    protected $password;
    protected $sender;
    protected $phone;
    protected $msg;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($username, $password, $sender, $phone, $msg)
    {
        $this->token = '8b49c1406246765709bfdbaa6b8a9232';
        $this->username = $username;
        $this->password = $password;
        $this->sender = $sender;
        $this->phone = $phone;
        $this->msg = $msg;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new \GuzzleHttp\Client();
        $url = "https://ovalbsms.co.tz/api/send-sms";
        $data = array(
            'form_params' => array(
                'username' => $this->username,
                'password' => $this->password,                            
                'sender' => $this->sender,
                'receiver' =>array($this->phone),
                'message' => $this->msg,
            ),
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
                'Accept' => 'application/json',
            ],
        );
        
        $req = $client->post($url,  $data);
        $response = $req->getBody();
        $result = json_decode($response);
        // Log::info($result);
    }
}
