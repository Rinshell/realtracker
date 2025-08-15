<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Techno;
use App\Models\Status;
use App\Models\Event;

class ProductController extends Controller
{
    public $defaultPath= '/uploads/default.png';

    protected function enableAdmin()
    {
        return Auth::user()->can("edit product") || Auth::user()->can("edit user");
    }

   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $user= Auth::user();

        if ($user->can('reading')) {
            $products = Product::where('title','<>','none')->get();
            return Inertia::render('Products', [
                    'products'    => $products,
                    'enableAdmin' => $this->enableAdmin(),
                ]);
        } else {
            return redirect()->route('profile.edit');
        }
    }

    public function allProducts()
    {
        return response()->json([
            'products'    => Product::where('title','<>','none')->get(),
        ]);
    }

    public function getProduct($id)
    {
        $product = Product::find($id);

        $events   = Event::where([['product_id', $product->id],['active', true]])->get();
        $sns= array();
        foreach ($events as $ev) { 
            $sns[]= $ev->sn_n;
        }
        $table= [];
        foreach (array_unique($sns, SORT_NUMERIC) as $n) {
            $events_sn= Event::where([['product_id',$product->id],['sn_n',$n],['active', true]])->orderBy('date', 'asc');
            $table+= [$n => $events_sn->get()->toArray()];
        }

        return Inertia::render('Product', [
            'product'     => $product,
            'tables'      => $table,
            'technos'     => Techno::withTrashed()->where('product_id', $product->id)->get(),
            'status'      => Status::withTrashed()->get(),
            'enableEdit'  => auth()->user()->can('edit event'),
            'enableAdmin' => $this->enableAdmin(),
        ]);

    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        return Inertia::render('Product/Create');
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        Log::debug('ProductController::store');

        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
        
        if ($request->hasFile('file')) {
            $request->validate([
                    'file'        => 'file|image|max:2048',
            ]);
        }

        $filePath= $this->defaultPath;
        if ($request->hasFile('file')) {
            $fileName = time().'-'.$request->file->getClientOriginalName();  
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
        }

        $product= Product::create(array_merge($request->post(), ['path' => $filePath]));

        return redirect()->route('service')->with('success','Product has been created successfully.');
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function edit($id)
    {
        $product= Product::find($id);
        Log::debug('ProductController::edit - ' . $product->id);
        return Inertia::render('Product/Edit', [
            'product' => $product
        ]);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id)
    {
        Log::debug('ProductController::update - ' . $id);

        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
        

        if ($request->hasFile('file')) {
            $request->validate([
                'file'        => 'file|image|max:2048',
            ]);
        }

        $p= Product::find($id);

        // https://laravel.com/docs/9.x/filesystem
        if ($request->hasFile('file')) {
            // delete old file
            if ($p->path != $this->defaultPath && Storage::exists($p->path)) {
                Storage::delete($p->path);
            }

            $fileName = time().'-'.$request->file->getClientOriginalName();  
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
            $p->path  = $filePath;
        }

        $p->title= $request->input('title');
        $p->description= $request->input('description');
        $p->update();

        return redirect()->route('service')->with('success','Product has been updated successfully');
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Product  $product
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        Product::find($id)->delete();
        return redirect()->route('service')->with('success','Product has been deleted successfully');
    }
}
