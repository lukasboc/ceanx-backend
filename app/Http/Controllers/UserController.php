<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getLatestUsers(Request $request)
    {
        return User::latest()->take(5)->get();
    }
}
