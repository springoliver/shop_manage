<?php

namespace App\Repositories\Admin;

use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PageRepository
{
    /**
     * Get all pages with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Page::orderBy('pageid', 'desc')->paginate($perPage);
    }

    /**
     * Create a new page.
     *
     * @param array $data
     * @return Page
     */
    public function createPage(array $data): Page
    {
        return Page::create($data);
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
        return $page->update($data);
    }

    /**
     * Delete a page.
     *
     * @param Page $page
     * @return bool|null
     */
    public function deletePage(Page $page): ?bool
    {
        return $page->delete();
    }

    /**
     * Find a page by ID.
     *
     * @param int $id
     * @return Page|null
     */
    public function findById(int $id): ?Page
    {
        return Page::find($id);
    }
}

