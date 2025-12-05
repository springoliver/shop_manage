<?php

namespace App\Repositories\Admin;

use App\Models\RequestedModules;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RequestedModuleRepository
{
    /**
     * Get all Requested Modules with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return getrequestedModules::orderBy('rmid', 'asc')->paginate($perPage);
    }


    /**
     * Update an existing RequestedModule.
     *
     * @param RequestedModules $requestedmodule
     * @param array $data
     * @return bool
     */
    public function updateRequestedModule(RequestedModules $requestedmodule, array $data): bool
    {
        return $requestedmodule->update($data);
    }

    /**
     * Delete a RequestedModule.
     *
     * @param RequestedModules $requestedmodule
     * @return bool|null
     */
    public function deleteRequestedModules(RequestedModules $requestedmodule): ?bool
    {
        return $requestedmodule->delete();
    }

    /**
     * Find a RequestedModules by ID.
     *
     * @param int $rmid
     * @return RequestedModules|null
     */
    public function findById(int $rmid): ?RequestedModules
    {
        return RequestedModules::find($rmid);
    }
}

