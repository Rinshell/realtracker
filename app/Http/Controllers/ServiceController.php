<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Support\Facades\Log;

use App\Models\Product;
use App\Models\Techno;
use App\Models\Status;

class ServiceController extends Controller
{
    public function index()
    {
        $products = Product::where('title','<>','none')->get();
        $technos = Techno::all();
        $status = Status::all();
        return Inertia::render('Service', [
            'products' => $products,
            'technos' => $technos,
            'status' => $status,
        ]);
    }
/*
    public function EventCreate()
    {
        Log::debug("EventCreate:");
        return Inertia::render('Service/CreateEvent');
    }

    public function EventEdit($id)
    {
        $event= Event::find($id);
        return Inertia::render('Service/EditEvent', [
            'event' => $event
        ]);
    }

    public function EventUpdate(Request $request, $id)
    {
        $request->validate([
            'product' => 'required',
            'name' => 'required',
            'description' => 'required',
        ]);
        
        Event::find($id)->fill($request->post())->save();

        return redirect()->route('service')->with('success','Event has been updated successfully');
    }

    public function EventDestroy($id)
    {
        Event::find($id)->delete();
        return redirect()->route('service')->with('success','Event has been deleted successfully');
    }

    public function EventStore(Request $request)
    {
        Log::debug("EventStore");

        $request->validate([
            'product_id' => 'required|numeric|min:1',
            'name' => 'required',
            'description' => 'required',
        ]);

        Log::debug($request->post());
        
        Event::create($request->post());

        return redirect()->route('service')->with('success','Event has been created successfully.');
    }
*/


}
