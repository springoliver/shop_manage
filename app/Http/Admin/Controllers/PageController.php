<?php

namespace App\Http\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use App\Services\Admin\PageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * The page service instance.
     *
     * @var PageService
     */
    protected PageService $pageService;

    /**
     * Create a new controller instance.
     *
     * @param PageService $pageService
     */
    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $pages = $this->pageService->getPages(15);

        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Page $page
     * @return View
     */
    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PageRequest $request
     * @param Page $page
     * @return RedirectResponse
     */
    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        $validated = $request->validated();

        $this->pageService->updatePage($page, $validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Change the status of the page.
     *
     * @param \Illuminate\Http\Request $request
     * @param Page $page
     * @return RedirectResponse
     */
    public function changeStatus(\Illuminate\Http\Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Enable,Disable',
        ]);

        $page->update(['status' => $validated['status']]);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Status changed successfully.');
    }
}

