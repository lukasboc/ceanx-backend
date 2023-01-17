<?php

namespace App\Http\Controllers;

use App\Models\CostEstimation;
use App\Models\JiraConnection;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JiraConnectionController extends Controller
{
    public function createNewJiraConnection(Request $request)
    {
        $inputs = $request->input();

        $jc = new JiraConnection();
        $jc->title = $inputs['title'];
        $jc->description = $inputs['description'];
        $jc->host = $inputs['host'];
        $jc->email = $inputs['email'];
        $jc->api_token = $inputs['api_token'];

        $user = User::find($request->user()->id);
        $user->jiraConnections()->save($jc);

        return $jc;
    }

    public function testJiraConnection(Request $request)
    {
        $inputs = $request->input();

        $host = $inputs['host'];
        $email = $inputs['email'];
        $api_token = $inputs['api_token'];

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($email . ':' . $api_token),
            'Content-Type' => 'application/json',
            'X-Atlassian-Token' => 'no-check',
        ])->get($host. '/rest/api/2//permissions');

        return $response;
    }

    public function getJiraConnections(Request $request)
    {
        return JiraConnection::orderByDesc('updated_at')->get();
    }

    public function getJiraConnectionById($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(JiraConnection::with('user:id,name')->find($id));
    }

    public function getSearchResult(Request $request)
    {
        $inputs = $request->input();

        $text = $inputs['text'];
        $components = $inputs['components'];

        $jql = 'timespent != null AND ';
        if($text !== '' && $text !== null){
            $jql .= 'text ~ "' . $text . '"';
            if(sizeof($components) !== 0){
                $jql .= ' AND component = "' . $components[0] . '"';
                $count = 0;
                if(sizeof($components) > 0){
                    foreach ($components as $component){
                        if($count === 0) continue;
                        $jql .= ' OR ';
                        $jql .= $component;
                        $count++;
                    }
                }
            }
        } elseif (sizeof($components) !== 0){
            $jql .= 'component = "' . $components[0] . '"';
            $count = 0;
            if(sizeof($components) > 0){
                foreach ($components as $component){
                    if($count === 0) continue;
                    $jql .= ' OR ';
                    $jql .= $component;
                    $count++;
                }
            }
        } else {
            return response()->json('Ohne Eingabe, keine Ergebnisse.',400);
        }

        $jiraConnections = JiraConnection::orderByDesc('updated_at')->get();

        $result = array();
        foreach ($jiraConnections as $jiraConnection) {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($jiraConnection->email . ':' . $jiraConnection->api_token),
                'Content-Type' => 'application/json',
                'X-Atlassian-Token' => 'no-check',
            ])->get($jiraConnection->host. '/rest/api/2/search',[
                'jql' => $jql,
                'fields' => 'timespent,issueType,project,updated,status,assignee,progress,summary'
            ]);
            $json = $response->json();

            foreach ($json['issues'] as $searchResult) {
                $result[] = $searchResult;
            }
        }
        return $result;
    }

    public function deleteConnection($id): \Illuminate\Http\JsonResponse
    {
        $con = JiraConnection::find($id);
        $con->delete();
        return response()->json('The connection was deleted',200);
    }
}
