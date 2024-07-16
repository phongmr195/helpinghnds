<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use App\Services\Api\DvService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    use ApiResponser;

    protected $dvService;

    function __construct(DvService $dvService)
    {
        $this->dvService = $dvService;
    }

    /**
     * Get list service
     */
    function getListService(Request $request)
    {
        
        $store = 'redis';
        // $value = null;
        $value = Cache::store($store)->get('services');

        //neu chua co lay data va tao cache
        if (empty($value)) {
            $value = $this->dvService->getListService();
            //luu cache mai mai
            Cache::store($store)->put('services', $value); //600 => 10 minute
        }
        
        //xu ly khi user khac vn: la usd hay vnd
        $user = $request->user();
        if(!empty($user) && !empty($user->nation_code) && $user->nation_code != 'vn'){
            $value[0]['user'] = $user;
            foreach ($value as $key => $data) {
                $value[$key]['unit_vn'] = $data['unit_en'];
                $value[$key]['price_vn'] = $data['price'];

                //xac dinh dang dong tien theo user da dang nhap
                //mac dinh la VND
                $value[$key]['currency'] = 'USD';
            }
        }

        return $this->success($value);
    }
}
