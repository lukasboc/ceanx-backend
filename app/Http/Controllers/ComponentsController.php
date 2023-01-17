<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\JiraConnection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ComponentsController extends Controller
{
    public function createNewComponent(Request $request)
    {
        $inputs = $request->input();

        $component = new Component();
        $component->title = $inputs['title'];

        $user = User::find($request->user()->id);
        $user->components()->save($component);

        return $component;
    }

    public function getComponents(Request $request)
    {
        return Component::orderByDesc('title')->get();
    }

    public function deleteComponent($id): \Illuminate\Http\JsonResponse
    {
        $com = Component::find($id);
        $com->delete();
        return response()->json('The component was deleted',200);
    }

}
