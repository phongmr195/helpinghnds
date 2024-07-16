<?php

namespace App\Services\Admin;

use App\Models\Setting;
use App\Services\BaseService;
use Illuminate\Support\Str;

/**
 * Class SettingService
 */
class SettingService extends BaseService
{
    /**
     * SettingService construct
     * @param Setting $setting
     */
    public function __construct(Setting $settings)
    {
        $this->model = $settings;
    }

    /**
     * Create one setting
     * @param array $data
     * @return Setting $setting
     */
    public function createSetting(array $data = []) : Setting
    {
        return $this->model->create([
            'key' => Str::slug($data['key']),
            'value' => $data['value']
        ]);
    }
    
    /**
     * Update setting
     * @param array $data
     * @return Setting
     */
    public function updateSetting(array $data = []) : Setting
    {
        return $this->model->where('key', $data['key'])->update([
            'value' => $data['value']
        ]);
    }

    /**
     * Get list setting
     * @return Setting collection
     */
    public function getListSettings()
    {
        return $this->model->select('id', 'key', 'value')->get();
    }

    /**
     * Get detail setting
     * @param Setting @setting
     * @return $setting
     */
    public function getSettingDetail(Setting $setting)
    {
        return $setting;
    }
}