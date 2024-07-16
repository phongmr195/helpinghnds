<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class OffWorkerWithoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = -1;
    public $backoff = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // throw new Exception('Failed!', 999);
        Log::info('set offline status for worker is start!');

        User::query()
            ->where('user_type', User::IS_WORKER)
            ->where('worker_status', User::WORKER_ON)
            ->where('updated_at', '<=', now()->subMinutes(config('constant.worker_online_more_minutes')))
            ->update([
                'worker_status' => User::WORKER_OFF
            ]);

        Log::info('set offline status for worker is running!');
    }

    /**
     * Job failed
     */
    public function failed(Throwable $th)
    {
        if ($th->getCode() == 999) {
            $this->release();
        } else {
            Log::info($th->getMessage());
        }
    }

    public function retryUntil()
    {
        return now()->addSeconds(30);
    }
}
