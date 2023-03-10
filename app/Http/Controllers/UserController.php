<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
/**
 * @group User
 *
 * Endpoints for handling User Requests.
 * @authenticated
 */
class UserController extends Controller
{
    public function getLatestUsers(Request $request)
    {
        return User::latest()->take(5)->get();
    }
}
