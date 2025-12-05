<?php

namespace App\Repositories\Admin;

use App\Models\EmailFormat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmailFormatRepository
{
    /**
     * Get all email formats with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return EmailFormat::orderBy('emailid', 'desc')->paginate($perPage);
    }

    /**
     * Update an email format.
     *
     * @param EmailFormat $emailFormat
     * @param array $data
     * @return bool
     */
    public function updateEmailFormat(EmailFormat $emailFormat, array $data): bool
    {
        return $emailFormat->update($data);
    }
}

