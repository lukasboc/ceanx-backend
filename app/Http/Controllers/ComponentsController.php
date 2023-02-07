<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\JiraConnection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
/**
 * @group Components
 *
 * Endpoints for handling Component Requests.
 * @authenticated
 */
class ComponentsController extends Controller
{
    /**
     * Create a new Component.
     *
     * This endpoint allows you to add new Component.
     * @bodyParam Component object required
     * @bodyParam Component.title string required Title
     */
    public function createNewComponent(Request $request)
    {
        $inputs = $request->input();

        $component = new Component();
        $component->title = $inputs['title'];

        $user = User::find($request->user()->id);
        $user->components()->save($component);

        return $component;
    }

    /**
     * Get all Components.
     *
     * This endpoint allows you to get all Component.
     */
    public function getComponents(Request $request)
    {
        return Component::orderByDesc('title')->get();
    }

    /**
     * Delete a Component.
     *
     * This endpoint allows you to delete a Component.
     */
    public function deleteComponent($id): \Illuminate\Http\JsonResponse
    {
        $com = Component::find($id);
        $com->delete();
        return response()->json('The component was deleted',200);
    }

}
