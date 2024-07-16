<?php

namespace App\Http\Controllers\Api\PageStatic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\UploadService;
use App\Services\Api\FcmService;
use App\Http\Requests\Api\Page\UploadImageRequest;
use App\Http\Requests\Api\Page\UploadMultiImageRequest;
use App\Traits\ApiResponser;

//use cache
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class StaticController extends Controller
{

    use ApiResponser;

    protected $uploadService;
    protected $fcmService;

    private $google_key = 'AIzaSyDneO6KXu_qZWPasIcQS5YIbcWQDXecZqQ';

    public function __construct(UploadService $uploadService, FcmService $fcmService)
    {
        $this->uploadService = $uploadService;
        $this->fcmService = $fcmService;
    }

    public function uploadImage(UploadImageRequest $request)
    {
        $image = $this->uploadService->uploadImage($request->image);
        return $this->success($image);
    }

    /**
     * Upload multi image
     */
    public function uploadMultiImage(UploadMultiImageRequest $request)
    {
        $images = $this->uploadService->uploadMultiImage($request->images);
        return $this->success($images);
    }

    public function demoPushNoti()
    {
        $deviceToken = 'dgDQ9hIsQwyK-ncSpAmiL8:APA91bHFJ4zW1ukfAdHAnpld1nCoZwzyQQ0gXQkbu6pW3thveEDas0SFmE4pNFxBXJ--Thh6URjNzU_Tvupvl-wuap7OL_kqh-tgKgKV8ElEqWBXPvdnEABJt2Zj5NOG28fDxDkLDNWr';
        $notification = $this->fcmService->createNotification();
        $noti = $this->fcmService->sendNotification($deviceToken, $notification, 'android');
        return $noti;
    }

    /**
     * 
     * @param Request $request
     */
    public function googleGeoCoding(Request $request)
    {
        if (!isset($request->address)) {
            return $this->badRequest(400, 'Bad request', 'Failed...');
        }

        $keyCache = 'address=' . $request->address;
        $value = Cache::store('redis')->get($keyCache);
        if (!empty($value)) {
            return $this->success($value);
        }

        $urlGeo = "https://maps.google.com/maps/api/geocode/json?key=" . $this->google_key . "&address=" . $request->address;
        $response = Http::get($urlGeo);
        if ($response && $response->ok()) {
            $jsonData = $response->json();
            Cache::store('redis')->put($keyCache, $jsonData, 300); // 5 Minutes
            return $this->success($jsonData);
        }

        // $data = file_get_contents("https://maps.google.com/maps/api/geocode/json?key=" . $this->google_key . "&address=" . $request->address);
        // if ($data) {
        //     $data = json_decode($data);
        // }
        return $this->success(null);
    }
}
