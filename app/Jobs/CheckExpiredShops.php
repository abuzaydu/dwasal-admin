<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;
use Log;

class CheckExpiredShops implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Checking for Expired Shops');
        $payments = Payment::where('expire_date', '<', \Carbon\Carbon::Now())->get();
        foreach ($payments as $key => $payment) {
            $payment->is_expired = true;
            $payment->save();
        }
    }
}
