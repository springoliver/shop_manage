<?php

namespace App\Services\Admin;

use App\Models\RequestedModule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RequestedModuleService
{
    /**
     * Get all requested modules with related data.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getRequestedModules(int $perPage = 15): LengthAwarePaginator
    {
        return DB::table('stoma_request_module as rm')
            ->select([
                'rm.*',
                's.store_name',
                's.storeownerid',
                's.typeid',
                's.store_email',
                'sto.firstname',
                'sto.lastname',
                'st.store_type'
            ])
            ->join('stoma_store as s', 's.storeid', '=', 'rm.storeid')
            ->leftJoin('stoma_storeowner as sto', 'sto.ownerid', '=', 's.storeownerid')
            ->leftJoin('stoma_storetype as st', 'st.typeid', '=', 's.typeid')
            ->orderBy('rm.rmid', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get requested module by ID with related data.
     *
     * @param int $rmid
     * @return object|null
     */
    public function getRequestedModuleById(int $rmid): ?object
    {
        return DB::table('stoma_request_module as rm')
            ->select([
                'rm.*',
                's.storeid',
                's.storeownerid',
                's.store_email',
                'sto.username'
            ])
            ->leftJoin('stoma_store as s', 's.storeid', '=', 'rm.storeid')
            ->leftJoin('stoma_storeowner as sto', 'sto.ownerid', '=', 's.storeownerid')
            ->where('rm.rmid', $rmid)
            ->first();
    }

    /**
     * Update the status of a requested module.
     *
     * @param RequestedModule $requestedModule
     * @param string $status
     * @return bool
     */
    public function updateStatus(RequestedModule $requestedModule, string $status): bool
    {
        return $requestedModule->update(['status' => $status]);
    }
}

