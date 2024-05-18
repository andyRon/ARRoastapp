<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCafeRequest;
use App\Models\Cafe;
use Illuminate\Support\Facades\Request;

class CafesController extends Controller
{
    public function getCafes()
    {
        $cafes = Cafe::all();
        return response()->json($cafes);
    }

    public function getCafe($id)
    {
        $cafe = Cafe::where('id', '=', $id)->first();
        return response()->json($cafe);
    }

    public function postNewCafe(StoreCafeRequest $request)
    {
        $cafe = new Cafe();
        $cafe->name = $request->input('name');
        $cafe->address = $request->input('address');
        $cafe->city = $request->input('city');
        $cafe->state = $request->input('state');
        $cafe->zip = $request->input('zip');

        $cafe->save();

        return response()->json($cafe, 201);
    }
}
