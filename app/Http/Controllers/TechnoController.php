<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Product;
use App\Models\Techno;

class TechnoController extends Controller
{
    public function create()
    {
        Log::debug("Techno::create");
        return Inertia::render('Service/TechnoCreate');
    }

    public function edit($id)
    {
        Log::debug("Techno::edit - " . $id);
        $t= Techno::find($id);
        return Inertia::render('Service/TechnoEdit', [
            'techno' => $t
        ]);
    }

    public function update(Request $request, $id)
    {
        Log::debug("Techno::update - " . $id);
        $request->validate([
            'product_id' => 'required|numeric|min:0',
            'title' => 'required',
            'order' => 'required|numeric'
        ]);
        
        Techno::find($id)->fill($request->post())->save();

        return redirect()->route('service')->with('success','Techno has been updated successfully');
    }

    public function destroy($id)
    {
        Log::debug("Techno::destroy - " . $id);
        Techno::find($id)->delete();
        return redirect()->route('service')->with('success','Techno has been deleted successfully');
    }

    public function store(Request $request)
    {
        Log::debug("Techno::store");
        Log::debug($request->post());

        $request->validate([
            'product_id' => 'required|numeric|min:0',
            'title' => 'required',
            'order' => 'required|numeric'
        ]);

        Techno::create($request->post());

        return redirect()->route('service')->with('success','Techno has been created successfully.');
    }
}
