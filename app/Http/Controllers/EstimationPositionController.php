<?php

namespace App\Http\Controllers;

use App\Models\CostEstimation;
use App\Models\EstimationPosition;
use App\Models\JiraConnection;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
/**
 * @group Estimation Positions
 *
 * Endpoints for handling Estimation Position Requests.
 * @authenticated
 */
class EstimationPositionController extends Controller
{
    /**
     * Ceeate a new Cost Estimation Position.
     *
     * This endpoint allows you to add new Cost Estimation Position.
     * @bodyParam costEstimationPosition object required
     * @bodyParam costEstimationPosition.title string required Title
     * @bodyParam costEstimationPosition.description Description
     * @bodyParam costEstimationPosition.component string required Component/Group
     * @bodyParam costEstimationPosition.comment string Comment
     * @bodyParam costEstimationPosition.minimum_estimate string required Minimum Estimate (as a comma-separated decimal or int) Examples: "10,75" or 10
     * @bodyParam costEstimationPosition.maximum_estimate string required Maximum Estimate (as a comma-separated decimal or int) Examples: "10,75" or 10
     */
    public function createNewCostEstimationPosition(Request $request)
    {
        $inputs = $request->input();

        $position = new EstimationPosition();
        $position->title = $inputs['title'];
        $position->description = $inputs['description'];
        $position->component = $inputs['component'];
        $position->comment = $inputs['comment'];
        $position->minimum_estimate = str_replace(',', '.',$inputs['minimum_estimate']);
        $position->maximum_estimate = str_replace(',', '.',$inputs['maximum_estimate']);

        $ce = CostEstimation::find($inputs['cost_estimation_id']);

        $ce->estimationPositions()->save($position);

        $position->user()->associate(User::find($request->user()->id));
        $position->save();

        return $position;
    }

    /**
     * Edit a Cost Estimation Position.
     *
     * This endpoint allows you to edit a Cost Estimation Position.
     * @bodyParam costEstimationPosition object required
     * @bodyParam costEstimationPosition.id string required ID
     * @bodyParam costEstimationPosition.title string required Title
     * @bodyParam costEstimationPosition.description Description
     * @bodyParam costEstimationPosition.component string required Component/Group
     * @bodyParam costEstimationPosition.comment string Comment
     * @bodyParam costEstimationPosition.minimum_estimate string required Minimum Estimate (as a comma-separated decimal or int) Examples: "10,75" or 10
     * @bodyParam costEstimationPosition.maximum_estimate string required Maximum Estimate (as a comma-separated decimal or int) Examples: "10,75" or 10
     */
    public function editCostEstimationPosition(Request $request)
    {
        $inputs = $request->input();

        $position = EstimationPosition::find($inputs['id']);

        $position->title = $inputs['title'];
        $position->description = $inputs['description'];
        $position->component = $inputs['component'];
        $position->comment = $inputs['comment'];
        $position->minimum_estimate = str_replace(',', '.',$inputs['minimum_estimate']);
        $position->maximum_estimate = str_replace(',', '.',$inputs['maximum_estimate']);
        $position->save();

        return response()->json(EstimationPosition::find($inputs['id']));
    }

    /**
     * Rename Component of Cost Estimation Positions.
     *
     * This endpoint allows you to edit a Cost Estimation Position.
     * @bodyParam object object required
     * @bodyParam object.old_name string required Old Name
     * @bodyParam object.new_name string required New Name
     */
    public function renameComponent(Request $request)
    {
        $inputs = $request->input();

        $costEstimationId = $inputs['cost_estimation_id'];
        $oldName = $inputs['old_name'];
        $newName = $inputs['new_name'];

        if($oldName === '' || $oldName === null || $newName === '' || $newName === null){
            return response()->json('Ohne Eingabe, keine Umbenennung.',400);
        }

        EstimationPosition::where('component', $oldName)
            ->where('cost_estimation_id', $costEstimationId)
            ->update(['component' => $newName]);

        return response()->json($oldName .' zu ' . $newName . ' umbenannt.',200);
    }

    /**
     * Get all Positions for a Cost Estimation.
     *
     * This endpoint allows you to get all Cost Estimation Position for a Cost Estimation.
     */
    public function getCostEstimationById($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(CostEstimation::find($id)->estimationPositions);
    }

    /**
     * Delete a Cost Estimation Position.
     *
     * This endpoint allows you to delete a Cost Estimation Position.
     */
    public function deletePosition($id): \Illuminate\Http\JsonResponse
    {
        $position = EstimationPosition::find($id);
        $position->delete();
        return response()->json('The position was deleted',200);
    }

    /**
     * Get a specific Cost Estimation Position.
     *
     * This endpoint allows you to get a specific Cost Estimation Position.
     */
    public function getCostEstimationPositionById($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(EstimationPosition::with('user:id,name')->find($id));
    }

    /**
     * Search CeanX Calculation Positions.
     *
     * This endpoint allows you to search for a String in all
     * stored CeanX Cost Estimation Positions.
     * @bodyParam text string Search String
     */
    public function getSearchResult(Request $request)
    {
        $inputs = $request->input();

        $text = $inputs['text'];

        if($text === '' || $text === null){
            return response()->json('Ohne Eingabe, keine Ergebnisse.',400);
        }

        //$result = DB::table('estimation_positions')
        //    ->where('title','LIKE',"%{$text}%")
        //    ->orWhere('component','LIKE',"%{$text}%")
        //    ->get();

        $result = DB::table('estimation_positions')
            ->orWhere('title', 'LIKE', "%{$text}%")
            ->orWhere('component', 'LIKE', "%{$text}%")
            ->get();

        $response = array();
        foreach ($result as $dbresult) {
            $response[] = EstimationPosition::find($dbresult->id);
        }
        return $response;
    }
}
