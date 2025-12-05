<?php

namespace App\Services\Admin;

use App\Models\RequestedModules;
use App\Repositories\Admin\RequestedModuleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SuggestionsService
{
    /**
     * The module requests repository instance.
     *
     * @var RequestedModuleRepository
     */
    protected RequestedModuleRepository $repository;

    /**
     * Create a new service instance.
     *
     * @param RequestedModuleRepository $repository
     */
    public function __construct(RequestedModuleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated module requests.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getrequestedModules(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage);
    }

}

