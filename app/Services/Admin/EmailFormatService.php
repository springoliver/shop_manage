<?php

namespace App\Services\Admin;

use App\Models\EmailFormat;
use App\Repositories\Admin\EmailFormatRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmailFormatService
{
    /**
     * The email format repository instance.
     *
     * @var EmailFormatRepository
     */
    protected EmailFormatRepository $repository;

    /**
     * Create a new service instance.
     *
     * @param EmailFormatRepository $repository
     */
    public function __construct(EmailFormatRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated email formats.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getEmailFormats(int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage);
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
        return $this->repository->updateEmailFormat($emailFormat, $data);
    }
}

