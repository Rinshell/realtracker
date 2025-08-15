<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;

//use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use App\Models\Product;
use App\Models\Event;
use App\Models\File;

class EventController extends Controller
{
    public function create()
    {
        $sn= intval(parse_url(url()->full(), PHP_URL_QUERY));
        Log::debug("Event::create sn=".$sn);
        return Inertia::render('Event/Create', ['sn' => $sn]);
    }

    public function edit($id)
    {
        Log::debug("Event::edit - " . $id);
        $event= Event::find($id);
        return Inertia::render('Event/Edit', [
            'event' => $event
        ]);
    }

    public function update(Request $request, $id)
    {
        Log::debug("Event::update - " . $id);
        Log::debug($request->post());

        $request->validate([
            'date'          => 'required|date',
            'product_id'    => 'required|numeric|min:0',
            'sn_n'          => 'required|numeric|min:0',
            'sn_m'          => 'required|numeric|min:0',
            'sn_p'          => 'required|numeric|min:0',
            'description'   => '',
            'techno_id'     => 'required|numeric|min:0',
            'status_id'     => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('file')) {
            $request->validate([
                'file'        => 'file|mimes:txt,txr,rep|max:2048',
            ]);
        }
        $evt= Event::find($id);
        $evt->date= $request->input('date');
        $evt->sn_n= $request->input('sn_n');
        $evt->sn_m= $request->input('sn_m');
        $evt->sn_p= $request->input('sn_p');
        $evt->description= $request->input('description');
        $evt->techno_id= $request->input('techno_id');
        $evt->status_id= $request->input('status_id');
        $evt->update();


        // https://laravel.com/docs/9.x/filesystem
        if ($request->hasFile('file')) {
            if (count($evt->files)) {
                $destination = 'storage/'.$evt->files[0]->path;
                if(Storage::exists($destination))
                {
                    Storage::delete($destination);
                }
                File::find($evt->files[0]->id)->delete();
            }

            $fileName = time().'.'.$request->file->extension();  
            $filePath = $request->file('file')->storeAs('uploads/'.$evt->product_id, $fileName, 'public');
            File::create(  ["name" => $request->file('file')->getClientOriginalName(),
                            "path" => $filePath,
                            "event_id" => $evt->id,
                        ]);
        }

        return redirect()->route('product', $evt->product_id)->with('success','Event has been updated successfully');
    }

    public function destroy($id)
    {
        Log::debug("Event::destroy - " . $id);
        $evt= Event::find($id);
        $evt->active= false;
        $evt->save();
//        if(File::exists($evt->files[0]))
//        {
//            File::delete($evt->files[0]->path);
//        }        
//        return back()->withInput();
        return redirect()->route('product', $evt->product_id)->with('success','Techno has been deleted successfully');
    }

    public function store(Request $request)
    {
        Log::debug("Event::store");

        $id= $request->post()["product_id"];

        $request->validate([
            'date'          => 'required|date',
            'product_id'    => 'required|numeric|min:0',
            'sn_n'          => 'required|numeric|min:0',
            'sn_m'          => 'required|numeric|min:0',
            'sn_p'          => 'required|numeric|min:0',
            'description'   => '',
            'techno_id'     => 'required|numeric|min:0',
            'status_id'     => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('file')) {
            $request->validate([
                    'file'        => 'file|mimes:txt,txr,rep|max:2048',
            ]);
        }

        $evt= Event::create(array_merge($request->post(), ['user_id' => Auth::user()->id]));

        // https://laravel.com/docs/9.x/filesystem
        if ($request->hasFile('file')) {
            $fileName = time().'.'.$request->file->extension();  
//            $filePath= $request->file->move(public_path('uploads'), $fileName);

            $filePath = $request->file('file')->storeAs('uploads/'.$id, $fileName, 'public');
            File::create(  ["name" => $request->file('file')->getClientOriginalName(),
                            "path" => $filePath,
                            "event_id" => $evt->id,
                        ]);
            Log::debug("Create file: " . $filePath);
        }

        return to_route('product', $id)->with('success','Event has been created successfully.');
    }
}
