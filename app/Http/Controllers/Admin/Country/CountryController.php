<?php

namespace App\Http\Controllers\Admin\Country;

use App\Http\Controllers\Controller;
use App\Services\Admin\CountryService;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\AddCountryRequest;
use App\Http\Requests\Admin\EditCountryRequest;
use App\Models\Country;

class CountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index()
    {
        $countries = $this->countryService->listCountry();
        return view('admin.countries.index', compact('countries'));
    }

    public function viewAdd()
    {
        return view('admin.countries.add');
    }

    public function addCountry(AddCountryRequest $request)
    {
        $country = $this->countryService->createCountry($request->all());
        return redirect(route('admin.countries.list'));
    }

    public function editCountry(EditCountryRequest $request, Country $country)
    {
        $editCoutry = $this->countryService->updateCountry($country, $request->all());
        return redirect(route('admin.countries.list'));
    }

    public function detail(Country $country)
    {
        $country = $this->countryService->detail($country);
        return view('admin.countries.edit', compact('country'));
    }
}
