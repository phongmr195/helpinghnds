<?php

namespace App\Services\Admin;

use App\Models\Country;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Exceptions\GeneralException;

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
     * List users
     */
    public function listCountry()
    {
        return $this->model->select('id', 'alt', 'title', 'phone_code', 'is_default')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function detail(Country $country)
    {
        return $country;
    }

    public function createCountry(array $data = [])
    {
        DB::beginTransaction();

        try {
            $country = $this->model->create([
                'alt' => $data['alt'],
                'title' => $data['title'],
                'phone_code' => $data['phone_code']
            ]);
               
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem create this user. Please try again.'));
        }

        DB::commit();
        
        return $country;    
    }

    public function updateCountry(Country $country, array $data = [])
    {
        DB::beginTransaction();

        try {
            $country->update([
                'alt' => $data['alt'],
                'title' => $data['title'],
                'phone_code' => $data['phone_code']
            ]);
               
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem create this user. Please try again.'));
        }

        DB::commit();
        
        return $country; 
    }
}
