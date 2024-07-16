<?php

namespace App\Jobs;

use App\Services\Api\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\OrderStatus;
use Carbon\Carbon;

class NewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $jobType;

    private $jobData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jobType, $jobData)
    {
        $this->jobType = $jobType;
        $this->jobData = $jobData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->jobType) {
            case 'checkWorkerJob':
                $this->checkWorkerJob();
                break;

            default:
                # code...
                break;
        }
    }

    //kiem tra 1 job da tao sau 1 khoang thoi gian ma worker kg done thi cancel
    private function checkWorkerJob()
    {

        //neu kg co worker hoac work = 0
        if (
            empty($this->jobData['order_id'])
            && empty($this->jobData['worker_id'])
            && $this->jobData['worker_id'] <= 0
        ) {
            return false;
        }

        $model = new Order();
        $order = $model::query()->with('detail')
            ->where('id', $this->jobData['order_id'])
            ->where('worker_id', $this->jobData['worker_id'])
            ->where('order_status', OrderStatus::WAITING_WORKER_ACCEPT)
            ->first();
        //neu thoi gian work
        if ($order) {
            $updatedAt = Carbon::parse($order->updated_at)->addSeconds(30)->timestamp;
            //het thoi gian cho thi huy worker
            if ($updatedAt <= Carbon::now()->timestamp) {
                OrderLog::create([
                    'worker_id' => $order->worker_id,
                    'order_id' => $order->id,
                    'type' => 'cancel',
                    'by_user' => 'queue'
                ]);

                $order->update([
                    'worker_id' => 0,
                ]);

                //tim worker khac
            }
        }
        return true;
    }


    private function fcmDemo()
    {
        $token = 'evus8eIZRaCbjLo2ub0DmM:APA91bEaf4aqsTfFqPzoCii1xEEhoeovBR9hSESfFBboIaHkcgTE5k8cJIKt1nJlaOJfyKVQaBNyyULmOmg_dRVtk7eMqclNdtPM4D4P7h_gmlXxP6lPjaJQBqT4JEWfwm9Z-49Xp8oE';
        $notiData = [
            'order' => array(),
            'user' => array(),
            'worker' => array(),
            'title' => 'Assistant new work',
            'body' => '',
            'image' => 'https://helpinghnds-dev.giacongphanmem.vn/assets/images/icon-app-for-client.jpg',
            'fcm' => [
                'type' => 'FCM-TO-WORKER-NEW-JOB'
                //'type' => 'FCM-ORDER-DONE'
            ]
        ];

        $notiData["order"] = json_encode(array(
            "id" => 3171,
            "address" => "11212 Lê Văn Lương...",
            "service_name" => "sds sdsdsd sd s",
            "service_child_name" => "asasdsadasdsa",
            "note_description" => "dadadad adada da a",
        ));

        $FCM = new FcmService();
        $FCM->send($token, $notiData);
    }
}
