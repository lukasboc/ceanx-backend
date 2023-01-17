<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\CostEstimation;
use App\Models\EstimationPosition;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class CostEstimationController extends Controller
{
    public function createNewCostEstimation(Request $request): CostEstimation
    {
        $inputs = $request->input();

        $ce = new CostEstimation();
        $ce->title = $inputs['title'];
        $ce->description = $inputs['description'];
        $ce->unit = $inputs['unit'];
        $ce->unit_rate = $inputs['unit_rate'];

        $user = User::find($request->user()->id);
        $user->costEstimations()->save($ce);

        return $ce;
    }

    public function editCostEstimation(Request $request)
    {
        $inputs = $request->input();

        $ce = CostEstimation::find($inputs['id']);

        $ce->title = $inputs['title'];
        $ce->description = $inputs['description'];
        $ce->unit = $inputs['unit'];
        $ce->unit_rate = $inputs['unit_rate'];
        $ce->save();

        return response()->json(CostEstimation::find($inputs['id']));
    }

    public function getCostEstimations(Request $request)
    {
        return CostEstimation::orderByDesc('updated_at')->get();
    }

    public function getLatestCostEstimations(Request $request)
    {
        return CostEstimation::latest()->take(5)->get();
    }

    public function getCostEstimationById($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(CostEstimation::with('user:id,name')->find($id));
    }

    public function deleteCostEstimation($id): \Illuminate\Http\JsonResponse
    {
        $ce = CostEstimation::find($id);
        $ce->delete();
        return response()->json('The cost estimation was deleted',200);
    }

    public function duplicateCostEstimation(Request $request)
    {
        $inputs = $request->input();
        $user = User::find($request->user()->id);

        $costEstimation = CostEstimation::find($inputs['cost_estimation_id']);

        $clone = $costEstimation->replicate();
        $clone->user()->associate($user);

        $clone->save();

        $estimationPositions = $costEstimation->estimationPositions;

        foreach ($estimationPositions as $toClone){
            $clonedPosition = $toClone->replicate();
            $clonedPosition->user()->associate($user);
            $clonedPosition->costEstimation()->associate($clone);
            $clonedPosition->save();
        }

        return response()->json($clone, 201);
    }


}
