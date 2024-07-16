<?php

namespace App\Jobs;

use App\Services\Api\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FcmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $deviceTokens;

    private $notiData;

    private $deviceType;

    private $isNoti;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($deviceTokens, $notiData, $deviceType = 'android', $isNoti = false)
    {
        $this->deviceTokens = $deviceTokens;
        $this->notiData = $notiData;
        $this->deviceType = $deviceType;
        $this->isNoti = $isNoti;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $FCM = new FcmService();
        $FCM->send($this->deviceTokens, $this->notiData, $this->deviceType, $this->isNoti);
    }
}
