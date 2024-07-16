<?php

namespace App\Services\Api;

use App\Services\BaseService;
use App\Models\Service;

/**
 * DvService class
 */
class DvService extends BaseService
{
    private $fields = array(
        'id',
        'parent_id',
        'name',
        'img',
        'en',
        'unit_en',
        'vi',
        'unit_vn',
        'price',
        'price_vn'
    );

    public function __construct(Service $service)
    {
        $this->model = $service;
    }

    /**
     * Get list service
     * @return collection Service
     */
    public function getListService()
    {
        return $this->model->where('parent_id', false)
            ->with(['children' => function ($query) {
                $query->select($this->fields)
                    ->orderBy('sort');
            }])
            ->orderBy('sort')
            ->get($this->fields);
    }

    /**
     * Get service detail
     * @param $id
     * @return Service
     */
    public function getServiceDetail($id)
    {
        return $this->model->where('id', $id)
            ->select('id', 'name', 'price')
            ->first();
    }

    /**
     * Get service detail
     * @param array $data
     */
    public function getServiceWithName($name)
    {
        return $this->model->where('en', $name)
            ->orWhere('vi', $name)
            ->select('id', 'name', 'price')
            ->first();
    }
}
