<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

Route::withoutMiddleware('web')->group(function () {
    Route::get('/run-migrations/{secret}', function (Request $request) {
        if ($request->secret !== config('app.migration_secret')) {
            abort(403, config('app.migration_secret'));
        }
        Artisan::call('migrate', ['--force' => true]);
        return "<pre>".Artisan::output()."</pre>";
    });
});
