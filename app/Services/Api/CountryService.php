<?php

namespace App\Services\Api;

use App\Models\Country;
use App\Services\BaseService;

/**
 * Class UserService.
 */
class CountryService extends BaseService
{
    /**
     * UserService constructor.
     *
     * @param  Country  $country
     */
    public function __construct(Country $country)
    {
        $this->model = $country;
    }

    
    /**
     * List country
     */
    public function listCountry()
    {
        return Country::select('alt', 'title', 'phone_code')->get();
    }

}
