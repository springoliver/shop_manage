<?php

namespace App\Http\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmailFormatRequest;
use App\Models\EmailFormat;
use App\Services\Admin\EmailFormatService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmailFormatController extends Controller
{
    /**
     * The email format service instance.
     *
     * @var EmailFormatService
     */
    protected EmailFormatService $service;

    /**
     * Create a new controller instance.
     *
     * @param EmailFormatService $service
     */
    public function __construct(EmailFormatService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the email formats.
     *
     * @return View
     */
    public function index(): View
    {
        $emailFormats = $this->service->getEmailFormats();

        return view('admin.email-formats.index', compact('emailFormats'));
    }

    /**
     * Show the form for editing the specified email format.
     *
     * @param EmailFormat $emailFormat
     * @return View
     */
    public function edit(EmailFormat $emailFormat): View
    {
        return view('admin.email-formats.edit', compact('emailFormat'));
    }

    /**
     * Update the specified email format in storage.
     *
     * @param EmailFormatRequest $request
     * @param EmailFormat $emailFormat
     * @return RedirectResponse
     */
    public function update(EmailFormatRequest $request, EmailFormat $emailFormat): RedirectResponse
    {
        $this->service->updateEmailFormat($emailFormat, $request->validated());

        return redirect()
            ->route('admin.email-formats.index')
            ->with('success', 'Email format updated successfully.');
    }
}

