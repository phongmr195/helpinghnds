<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class HrmsController extends Controller
{
    use ApiResponser;
    private $accessToken;
    private $errTmp = array(
        "error" => "invalid_token",
        "error_description" => "The access token provided has expired"
    );

    /**
     * TMP Get from HRMS
     */
    public function __construct(Request $request)
    {
        $this->accessToken = $request->input('accessToken');
        if (empty($accessToken)) {
            return response()->json($this->errTmp);
        }
    }

    function kpi(Request $request)
    {
        $getKpi = $this->initCurl('kpis', $this->accessToken);
        return response()->json($getKpi);
    }

    function performanceReviews(Request $request)
    {
        $uri = 'performance_reviews';

        $empNumber = $request->input('empNumber');
        if ($empNumber) {
            $uri = "employee/$empNumber/performance_reviews";
        }

        $type = $request->input('type');
        if ($type) {
            $uri = $uri . '?type=' . $type;
        }

        $getPerformanceReviews = $this->initCurl($uri, $this->accessToken);
        return response()->json($getPerformanceReviews);
    }

    function postPerformanceReviews(Request $request)
    {
        $uri = 'performance_reviews';

        $empNumber = $request->input('empNumber');
        if ($empNumber) {
            $uri = "employee/$empNumber/performance_reviews";
        }

        $type = $request->input('type');
        if ($type) {
            $uri = $uri . '?type=' . $type;
        }

        $requesParams = $request->post();
        $requesParams['method'] = 'POST';

        $getPerformanceReviews = $this->initCurl($uri, $this->accessToken, $requesParams);

        return response()->json($getPerformanceReviews);
    }

    private function initCurl($uri, $accessToken, $requesParams = array())
    {
        $hostAPI = "https://vinaled.dragonhrm.com/symfony/web/index.php/api/v1/";
        // $hostAPI = "https://vinaledhrm.com/symfony/web/index.php/api/v1/";

        $method = 'GET';
        if (isset($requesParams['method'])) {
            $method = $requesParams['method'];
            unset($requesParams['method']);
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $hostAPI . $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer $accessToken"
            ),
            CURLOPT_POSTFIELDS => json_encode($requesParams)
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        }

        return json_decode($response);
    }
}
