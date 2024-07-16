<?php

namespace App\Services\Api;

use App\Jobs\FcmJob;

class FcmService
{

    public function __construct()
    {
    }

    //kg can tra ve
    function sendNotification($registatoin_ids, $notification, $device_type = 'android', $is_noti = false)
    {
        FcmJob::dispatch($registatoin_ids, $notification, $device_type, $is_noti);

        // $this->send($registatoin_ids, $notification, $device_type, $is_noti);

        return true;
    }

    //cho ket qua va tra ve > co tra ve
    function sendNotifyAndResult($registatoin_ids, $notification, $device_type = 'android', $is_noti = false)
    {
        return $this->send($registatoin_ids, $notification, $device_type, $is_noti);
    }

    /**
     * @param string $title
     * @param string $body
     * @param string $image
     * @param string $type [android|ios]
     * @return array $notification
     */
    public function createNotification($title = '', $body = '', $image = '', $type = '')
    {
        return [
            'title' => $title ?? 'Test Push Notification: ' . uniqid(),
            'body' => $body ?? 'Test Push Notification',
            'image' => $image ?? asset('/assets/images/helpinghnds-logo-app-client.jpg'),
            'type' => $type ?? 1
        ];
    }

    /**
     * Sending Push Notification Android and IOS
     * https://firebase.google.com/docs/cloud-messaging/http-server-ref
     * INPUT example:
     * $arrNotification= array();
     * $arrNotification["title"] = "PHP Push Notification: " .uniqid();		
     * $arrNotification["body"] ="PHP Push Notification";
     * $arrNotification["image"] = "https://helpinghnds-dev.giacongphanmem.vn/assets/images/icon-app-for-client.jpg";
     * $arrNotification["type"] = 1;
     */
    public function send($deviceToken, $notification, $deviceType = '', $is_noti = false)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        //neu kg co device plaform thi mac dinh la android
        if (empty($deviceType)) {
            $deviceType = 'android';
        }

        $notification += array(
            "image" => asset('/assets/images/icon-app-for-client.jpg'),
            "type" => 1
        );

        $fields = array(
            'to' => $deviceToken,
            'data' => $notification,
            'priority' => 'high'
        );

        //Foreground=> hien thi tren status bar if $isForeground
        //IOS required
        if ($deviceType == 'ios' || ($deviceType == "android" && $is_noti)) {
            // Required for background/quit data-only messages on iOS
            $fields['content_available'] = true;
            $notification['sound'] = 'sound/incoming051.mp3';
            $fields['notification'] = $notification;
        }

        // Firebase API Key
        $headers = array('Authorization: key=' . config('constant.firebase.keys.app_api_key'), 'Content-Type:application/json');

        // Open connection
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            // CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => $headers
        ));

        // Set the url, number of POST vars, POST data
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //CURLOPT_RETURNTRANSFER: 
        //----TRUE: waiting response
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Disabling SSL Certificate support temporarly
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);        
        curl_close($ch);

        //var_dump($result);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }       

        return $result;
    }

    //ham tra ve data khi response
    private function fcmResult($result = null)
    {
        return $result;
    }
}
