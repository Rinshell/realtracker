<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $user= Auth::user();

        if ($user->can('reading')) {
            $products = Product::where('title','<>','none')->get();
            return Inertia::render('Dashboard', [
                'products' => $products,
                'permissions' => Auth::user()->getPermissionsViaRoles(),
                ]);
        } else {
            return redirect()->route('profile.edit');
        }
    }
}
