<?php

namespace App\Repositories\Admin;

use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SettingRepository
{
    /**
     * Get all settings with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Setting::orderBy('settingid', 'asc')->paginate($perPage);
    }

    /**
     * Create a new setting.
     *
     * @param array $data
     * @return Setting
     */
    public function createSetting(array $data): Setting
    {
        return Setting::create($data);
    }

    /**
     * Update an existing setting.
     *
     * @param Setting $setting
     * @param array $data
     * @return bool
     */
    public function updateSetting(Setting $setting, array $data): bool
    {
        return $setting->update($data);
    }

    /**
     * Delete a setting.
     *
     * @param Setting $setting
     * @return bool|null
     */
    public function deleteSetting(Setting $setting): ?bool
    {
        return $setting->delete();
    }

    /**
     * Find a setting by ID.
     *
     * @param int $id
     * @return Setting|null
     */
    public function findById(int $id): ?Setting
    {
        return Setting::find($id);
    }
}

