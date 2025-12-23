<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('admin/users/Index', [
            'users' => User::query()
                ->paginate(10)
                ->onEachSide(2)
                ->withQueryString()
        ]);
    }
}
