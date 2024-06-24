<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    /**
     * GET /api/v1/companies/search
     * @param Request $request
     * @return JsonResponse
     */
    public function getCompanySearch(Request $request)
    {
        $term = $request->input('search');

        $companies = Company::where('name', 'LIKE', '%' . $term . '%')
            ->withCount('cafes')
            ->get();

        return response()->json(['companies' => $companies]);
    }
}
