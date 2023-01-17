<?php

namespace App\Http\Controllers;

use App\Models\JiraConnection;
use App\Models\TroiConnection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TroiConnectionController extends Controller
{
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

        return $tc;
    }

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

    public function getTroiConnections(Request $request)
    {
        return TroiConnection::orderByDesc('updated_at')->get();
    }

    public function getTroiConnectionById($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(TroiConnection::with('user:id,name')->find($id));
    }

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

    public function deleteConnection($id): \Illuminate\Http\JsonResponse
    {
        $con = TroiConnection::find($id);
        $con->delete();
        return response()->json('The conection was deleted',200);
    }
}
