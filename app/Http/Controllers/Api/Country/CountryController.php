<?php

namespace App\Http\Controllers\Api\Country;

use App\Http\Controllers\Controller;
use App\Exceptions\GeneralException;
use App\Traits\ApiResponser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\Log;
use App\Services\Api\CountryService;

class CountryController extends Controller
{
    use ApiResponser;
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * Get list countries
     * @return [json] list conutries
     */
    public function getListCountry()
    {
        $countries = $this->countryService->listCountry();
        return $this->success($countries);
    }
}
