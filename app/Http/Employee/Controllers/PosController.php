<?php

namespace App\Http\Employee\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    /**
     * Display the POS index page.
     */
    public function index(): View
    {
        return view('employee.pos.index');
    }

    /**
     * Display the POS main page with product categories.
     */
    public function main(): View
    {
        $employee = Auth::guard('employee')->user();
        
        // Get all catalog product categories for this store
        $catalogProductCategories = DB::table('stoma_catalog_product_category')
            ->where('storeid', $employee->storeid)
            ->orderBy('catalog_product_categoryid', 'ASC')
            ->get()
            ->map(function ($category) {
                return [
                    'catalog_product_categoryid' => $category->catalog_product_categoryid,
                    'catalog_product_category_name' => $category->catalog_product_category_name,
                    'catalog_product_category_colour' => $category->catalog_product_category_colour ?? 'CCCCCC',
                ];
            });
        
        return view('employee.pos.main', compact('catalogProductCategories'));
    }
}

