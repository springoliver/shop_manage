<?php

namespace App\Services\Admin;

use App\Models\Setting;
use App\Repositories\Admin\SettingRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SettingService
{
    /**
     * The setting repository instance.
     *
     * @var SettingRepository
     */
    protected SettingRepository $repository;

    /**
     * Create a new service instance.
     *
     * @param SettingRepository $repository
     */
    public function __construct(SettingRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated settings.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getSettings(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage);
    }

    /**
     * Save a new setting.
     *
     * @param array $data
     * @return Setting
     */
    public function saveSetting(array $data): Setting
    {
        return $this->repository->createSetting($data);
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
        return $this->repository->updateSetting($setting, $data);
    }

    /**
     * Delete a setting.
     *
     * @param Setting $setting
     * @return bool|null
     */
    public function deleteSetting(Setting $setting): ?bool
    {
        return $this->repository->deleteSetting($setting);
    }
}

