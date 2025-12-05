<?php

namespace App\Repositories\Admin;

use App\Models\StoreType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StoreTypeRepository
{
    /**
     * Get all store types with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return StoreType::orderBy('typeid', 'desc')->paginate($perPage);
    }

    /**
     * Create a new store type.
     *
     * @param array $data
     * @return StoreType
     */
    public function createStoreType(array $data): StoreType
    {
        return StoreType::create($data);
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
        return $storeType->update($data);
    }

    /**
     * Delete a store type.
     *
     * @param StoreType $storeType
     * @return bool|null
     */
    public function deleteStoreType(StoreType $storeType): ?bool
    {
        return $storeType->delete();
    }

    /**
     * Find a store type by ID.
     *
     * @param int $id
     * @return StoreType|null
     */
    public function findById(int $id): ?StoreType
    {
        return StoreType::find($id);
    }

    /**
     * Get all enabled store types.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEnabledStoreTypes()
    {
        return StoreType::where('status', 'Enable')
            ->orderBy('store_type', 'asc')
            ->get();
    }
}

