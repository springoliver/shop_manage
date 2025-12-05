<?php

namespace App\Services\Admin;

use App\Models\StoreType;
use App\Repositories\Admin\StoreTypeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StoreTypeService
{
    /**
     * The store type repository instance.
     *
     * @var StoreTypeRepository
     */
    protected StoreTypeRepository $repository;

    /**
     * Create a new service instance.
     *
     * @param StoreTypeRepository $repository
     */
    public function __construct(StoreTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated store types.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getStoreTypes(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage);
    }

    /**
     * Save a new store type.
     *
     * @param array $data
     * @return StoreType
     */
    public function saveStoreType(array $data): StoreType
    {
        return $this->repository->createStoreType($data);
    }

    /**
     * Update an existing store type.
     *
     * @param StoreType $storeType
     * @param array $data
     * @return bool
     */
    public function updateStoreType(StoreType $storeType, array $data): bool
    {
        return $this->repository->updateStoreType($storeType, $data);
    }

    /**
     * Delete a store type.
     *
     * @param StoreType $storeType
     * @return bool|null
     */
    public function deleteStoreType(StoreType $storeType): ?bool
    {
        return $this->repository->deleteStoreType($storeType);
    }

    /**
     * Get all enabled store types.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEnabledStoreTypes()
    {
        return $this->repository->getEnabledStoreTypes();
    }
}

