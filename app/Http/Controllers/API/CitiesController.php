<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    /**
     * 获取所有城市
     * @return JsonResponse
     */
    public function getCities(): JsonResponse
    {
        $cities = City::all();
        return response()->json($cities);
    }

    /**
     * 获取指定城市
     * @param $slug
     * @return JsonResponse
     */
    public function getCity($slug): JsonResponse
    {
        $city = City::query()->where('slug', '=', $slug)
            ->with(['cafes' => function ($query) {
                $query->with('company');
            }])
            ->first();
        if ($city != null) {
            return response()->json($city);
        } else {
            return response()->json(null, 404);
        }
    }
}
