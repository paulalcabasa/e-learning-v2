<?php

namespace App\Http\Controllers;

use App\Dealer;
use Illuminate\Http\Request;
use App\Http\Requests\DealerValidation;

class DealerController extends Controller
{
    public function index()
    {
        return response()->json(['dealers' => Dealer::oldest()->get()]);
    }

    public function show($dealer_id)
    {
        return response()->json(Dealer::findOrFail($dealer_id));
    }

    public function store(DealerValidation $request)
    {
        return response()->json(Dealer::create($request->all()));
    }

    public function update(DealerValidation $request, $dealer_id)
    {
        $dealer = Dealer::findOrFail($dealer_id);
        $dealer->dealer_name = $request->dealer_name;
        $dealer->branch = $request->branch;
        $dealer->save();

        return response()->json($dealer);
    }

    public function destroy($dealer_id)
    {
        $dealer = Dealer::findOrFail($dealer_id);
        $dealer->delete();

        return response()->json($dealer);
    }
}
