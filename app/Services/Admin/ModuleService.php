<?php

namespace App\Services\Admin;

use App\Models\Module;
use App\Repositories\Admin\ModuleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ModuleService
{
    /**
     * The module repository instance.
     *
     * @var ModuleRepository
     */
    protected ModuleRepository $repository;

    /**
     * Create a new service instance.
     *
     * @param ModuleRepository $repository
     */
    public function __construct(ModuleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated modules.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getModules(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage);
    }

    /**
     * Save a new module.
     *
     * @param array $data
     * @return Module
     */
    public function saveModule(array $data): Module
    {
        // Populate insert metadata
        $data['insertip'] = request()->ip();
        $data['insertby'] = auth('admin')->id() ?? 0;
        $data['editip'] = request()->ip();
        $data['editby'] = auth('admin')->id() ?? 0;

        return $this->repository->createModule($data);
    }

    /**
     * Update an existing module.
     *
     * @param Module $module
     * @param array $data
     * @return bool
     */
    public function updateModule(Module $module, array $data): bool
    {
        // Populate edit metadata
        $data['editip'] = request()->ip();
        $data['editby'] = auth('admin')->id() ?? 0;

        return $this->repository->updateModule($module, $data);
    }

    /**
     * Delete a module.
     *
     * @param Module $module
     * @return bool|null
     */
    public function deleteModule(Module $module): ?bool
    {
        return $this->repository->deleteModule($module);
    }
}

