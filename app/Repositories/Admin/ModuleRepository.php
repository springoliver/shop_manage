<?php

namespace App\Repositories\Admin;

use App\Models\Module;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ModuleRepository
{
    /**
     * Get all modules with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Module::orderBy('insertdate', 'desc')->paginate($perPage);
    }

    /**
     * Create a new module.
     *
     * @param array $data
     * @return Module
     */
    public function createModule(array $data): Module
    {
        return Module::create($data);
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
        return $module->update($data);
    }

    /**
     * Delete a module.
     *
     * @param Module $module
     * @return bool|null
     */
    public function deleteModule(Module $module): ?bool
    {
        return $module->delete();
    }

    /**
     * Find a module by ID.
     *
     * @param int $id
     * @return Module|null
     */
    public function findById(int $id): ?Module
    {
        return Module::find($id);
    }
}

