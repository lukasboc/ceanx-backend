<?php

namespace App\Http\Controllers;

use App\Models\JiraConnection;
use App\Models\TroiConnection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
/**
 * @group Troi Connections
 *
 * Endpoints for handling Troi Connection Requests.
 * @authenticated
 */
class TroiConnectionController extends Controller
{
    /**
     * Create a new Troi Connection.
     *
     * This endpoint allows you to add new Troi Connection.
     * @bodyParam troiConnection object required Connection
     * @bodyParam troiConnection.title string required Title
     * @bodyParam troiConnection.description string Description
     * @bodyParam troiConnection.host string required Host
     * @bodyParam troiConnection.username string required Username
     * @bodyParam troiConnection.password string required Password
     */
    public function createNewTroiConnection(Request $request)
    {
        $inputs = $request->input();

        $tc = new TroiConnection();
        $tc->title = $inputs['title'];
        $tc->description = $inputs['description'];
        $tc->host = $inputs['host'];
        $tc->username = $inputs['username'];
        $tc->password = $inputs['password'];

        $user = User::find($request->user()->id);
        $user->troiConnections()->save($tc);

        return $tc->makeHidden('password');
    }

    /**
     * Test a Troi Connection.
     *
     * This endpoint allows you to test a Troi Connection.
     * @bodyParam troiConnection object required Connection
     * @bodyParam troiConnection.host string required Host
     * @bodyParam troiConnection.username string required Username
     * @bodyParam troiConnection.password string required Password
     */
    public function testTroiConnection(Request $request)
    {
        $inputs = $request->input();

        $host = $inputs['host'];
        $username = $inputs['username'];
        $password = $inputs['password'];

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($username . ':' . md5($password)),
            'Content-Type' => 'application/json',
        ])->get($host. '/api/v2/rest/misc/verifyCredentials');
        return $response;
    }

    /**
     * Get all Troi Connections.
     *
     * This endpoint allows you retrieve all stored Troi Conenctions.
     */
    public function getTroiConnections(Request $request)
    {
        return TroiConnection::orderByDesc('updated_at')->get()->makeHidden('password');
    }

    /**
     * Get a specific Troi Connection.
     *
     * This endpoint allows retrieve a specific Troi Connection.
     */
    public function getTroiConnectionById($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(TroiConnection::with('user:id,name')->find($id)->makeHidden('password'));
    }

    /**
     * Search Troi Calculation Positions.
     *
     * This endpoint allows you to search for a String in all
     * stored Troi Connections.
     * @bodyParam text string required Search String
     */
    public function getSearchResult(Request $request)
    {
        $inputs = $request->input();

        $text = $inputs['text'];

        if($text === '' || $text === null){
            return response()->json('Ohne Eingabe, keine Ergebnisse.',400);
        }

        $troiConnections = TroiConnection::orderByDesc('updated_at')->get();

        $result = array();
        foreach ($troiConnections as $troiConnection) {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($troiConnection->username . ':' . md5($troiConnection->password)),
                'Content-Type' => 'application/json',
            ])->get($troiConnection->host. '/api/v2/rest/calculationPositions',[
                'clientId' => '*',
                'search' => $text,
                'favoritesOnly' => 'false',
                'withoutHourClosed' => 'false',
                'projectId' => '*'
            ]);
            $json = $response->json();

            foreach ($json as $jsonElement){
                $result[] = $jsonElement;
            }
        }
        return $result;
    }

    /**
     * Delete a Troi Connection.
     *
     * This endpoint allows you to delete a Troi Connection.
     */
    public function deleteConnection($id): \Illuminate\Http\JsonResponse
    {
        $con = TroiConnection::find($id);
        $con->delete();
        return response()->json('The conection was deleted',200);
    }
}
