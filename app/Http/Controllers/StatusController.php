<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;

use App\Models\Status;
use App\Models\Techno;

class StatusController extends Controller
{
    public function create()
    {
        Log::debug("Status::create:");
        return Inertia::render('Service/StatusCreate');
    }

    public function edit($id)
    {
        Log::debug("Status::edit - " . $id);
        $st= Status::find($id);
        return Inertia::render('Service/StatusEdit', [
            'status' => $st
        ]);
    }

    public function update(Request $request, $id)
    {
        Log::debug("Status::update - " . $id);
        $request->validate([
            'techno_id' => 'required|numeric|min:0',
            'title' => 'required',
            'level' => 'required',
            'order' => 'required|numeric'
        ]);
        
        Status::find($id)->fill($request->post())->save();

        return redirect()->route('service')->with('success','Status has been updated successfully');
    }

    public function destroy($id)
    {
        Log::debug("Status::destroy - " . $id);
        Status::find($id)->delete();
        return redirect()->route('service')->with('success','Status has been deleted successfully');
    }

    public function store(Request $request)
    {
        Log::debug("Status::store");
        Log::debug($request->post());

        $request->validate([
            'techno_id' => 'required|numeric|min:0',
            'title' => 'required',
            'level' => 'required',
            'order' => 'required|numeric'
        ]);

        Status::create($request->post());

        return redirect()->route('service')->with('success','Status has been created successfully.');
    }
}
