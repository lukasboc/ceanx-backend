<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\CostEstimation;
use App\Models\EstimationPosition;
use App\Models\JiraConnection;
use App\Models\Project;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class EstimationPositionController extends Controller
{
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

    public function getCostEstimationById($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(CostEstimation::find($id)->estimationPositions);
    }

    public function deletePosition($id): \Illuminate\Http\JsonResponse
    {
        $position = EstimationPosition::find($id);
        $position->delete();
        return response()->json('The position was deleted',200);
    }

    public function getCostEstimationPositionById($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(EstimationPosition::with('user:id,name')->find($id));
    }

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
