<?php

namespace App\Http\Controllers;

use App\Models\CostEstimation;
use App\Models\EstimationPosition;
use App\Models\User;
use Illuminate\Http\Request;
/**
 * @group Cost Estimations
 *
 * Endpoints for handling Cost Estimation Requests.
 * @authenticated
 */
class CostEstimationController extends Controller
{
    /**
     * Ceeate a new Cost Estimation.
     *
     * This endpoint allows you to add new Cost Estimation.
     * @bodyParam costEstimation object required
     * @bodyParam costEstimation.title string required Title
     * @bodyParam costEstimation.description Description
     * @bodyParam costEstimation.unit string Unit
     * @bodyParam costEstimation.unit_rate string Unit Rate
     */
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

    /**
     * Edit a Cost Estimation.
     *
     * This endpoint allows you to edit a Cost Estimation.
     * @bodyParam costEstimation object required
     * @bodyParam costEstimation.id string required ID
     * @bodyParam costEstimation.title string required Title
     * @bodyParam costEstimation.description Description
     * @bodyParam costEstimation.unit string Unit
     * @bodyParam costEstimation.unit_rate string Unit Rate
     */
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

    /**
     * Get all Cost Estimations.
     *
     * This endpoint allows you to retrieve all Cost Estimation.
     */
    public function getCostEstimations(Request $request)
    {
        return CostEstimation::orderByDesc('updated_at')->get();
    }

    /**
     * Get latest Cost Estimations.
     *
     * This endpoint allows you to retrieve the latest 5 Cost Estimation.
     */
    public function getLatestCostEstimations(Request $request)
    {
        return CostEstimation::latest()->take(5)->get();
    }

    /**
     * Get specific Cost Estimation.
     *
     * This endpoint allows you to retrieve a specific Cost Estimation.
     */
    public function getCostEstimationById($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(CostEstimation::with('user:id,name')->find($id));
    }

    /**
     * Delete Cost Estimation.
     *
     * This endpoint allows you to delete a specific Cost Estimation.
     */
    public function deleteCostEstimation($id): \Illuminate\Http\JsonResponse
    {
        $ce = CostEstimation::find($id);
        $ce->delete();
        return response()->json('The cost estimation was deleted',200);
    }

    /**
     * Duplicate a Cost Estimation.
     *
     * This endpoint allows you to edit a Cost Estimation.
     * @bodyParam costEstimation object required
     * @bodyParam costEstimation.cost_estimation_id string required ID
     */

    public function duplicateCostEstimation(Request $request)
    {
        $inputs = $request->input();
        $user = User::find($request->user()->id);

        $costEstimation = CostEstimation::find($inputs['cost_estimation_id']);

        $clone = $costEstimation->replicate();
        $clone->title = $clone->title . ' (Kopie)';
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
