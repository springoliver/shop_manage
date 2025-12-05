<?php

namespace App\Services\Admin;

use App\Models\StoreOwner;
use App\Models\UserGroup;
use App\Models\StoreType;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get all statistics counts.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'owner_count' => StoreOwner::count(),
            'usergroup_count' => UserGroup::count(),
            'category_count' => StoreType::count(),
            'department_count' => Department::count(),
        ];
    }

    /**
     * Get owner minimum signup date.
     *
     * @return string|null
     */
    public function getOwnerMinimumDate(): ?string
    {
        $result = DB::table('stoma_storeowner')
            ->select(DB::raw('MIN(signupdate) as mindate'))
            ->first();

        return $result?->mindate;
    }

    /**
     * Get owner details count for a specific date.
     *
     * @param string $date
     * @return int
     */
    public function getOwnerDetails(string $date): int
    {
        return StoreOwner::whereDate('signupdate', $date)
            ->where('status', 'Active')
            ->count();
    }

    /**
     * Get monthly owner count for a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    public function getMonthlyOwner(string $startDate, string $endDate): int
    {
        return StoreOwner::whereBetween(DB::raw('DATE(signupdate)'), [$startDate, $endDate])
            ->where('status', 'Active')
            ->count();
    }
}

