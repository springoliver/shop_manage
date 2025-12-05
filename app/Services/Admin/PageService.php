<?php

namespace App\Services\Admin;

use App\Models\Page;
use App\Repositories\Admin\PageRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PageService
{
    /**
     * The page repository instance.
     *
     * @var PageRepository
     */
    protected PageRepository $repository;

    /**
     * Create a new service instance.
     *
     * @param PageRepository $repository
     */
    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated pages.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPages(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage);
    }

    /**
     * Save a new page.
     *
     * @param array $data
     * @return Page
     */
    public function savePage(array $data): Page
    {
        // Add edit tracking information
        $data['edit_ip'] = request()->ip();
        $data['edit_by'] = auth()->id() ?? 0;

        return $this->repository->createPage($data);
    }

    /**
     * Update an existing page.
     *
     * @param Page $page
     * @param array $data
     * @return bool
     */
    public function updatePage(Page $page, array $data): bool
    {
        // Add edit tracking information
        $data['edit_ip'] = request()->ip();
        $data['edit_by'] = auth()->id() ?? 0;

        return $this->repository->updatePage($page, $data);
    }

    /**
     * Delete a page.
     *
     * @param Page $page
     * @return bool|null
     */
    public function deletePage(Page $page): ?bool
    {
        return $this->repository->deletePage($page);
    }
}

