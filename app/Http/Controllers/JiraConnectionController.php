<?php

namespace App\Http\Controllers;

use App\Models\CostEstimation;
use App\Models\JiraConnection;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
/**
 * @group Jira Connections
 *
 * Endpoints for handling Jira Connection Requests.
 * @authenticated
 */
class JiraConnectionController extends Controller
{
    /**
     * Create a new Jira Connection.
     *
     * This endpoint allows you to add new Troi Connection.
     * @bodyParam jiraConnection object required
     * @bodyParam jiraConnection.title string required Title
     * @bodyParam jiraConnection.description string Description
     * @bodyParam jiraConnection.host string required Host
     * @bodyParam jiraConnection.email string required E-Mail
     * @bodyParam jiraConnection.api_token string required API Token
     */
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

        return $jc->makeHidden('api_token');
    }

    /**
     * Test a Jira Connection.
     *
     * This endpoint allows you to add new Troi Connection.
     * @bodyParam jiraConnection object required Connection
     * @bodyParam jiraConnection.host string required Host
     * @bodyParam jiraConnection.email string required E-Mail
     * @bodyParam jiraConnection.api_token string required API Token
     */
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

    /**
     * Get all Jira Connections.
     *
     * This endpoint allows you retrieve all stored Jira Conenctions.
     */
    public function getJiraConnections(Request $request)
    {
        return JiraConnection::orderByDesc('updated_at')->get()->makeHidden('api_token');
    }

    /**
     * Get a specific Jira Connections.
     *
     * This endpoint allows you retrieve a specific Jira Conenction.
     */
    public function getJiraConnectionById($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(JiraConnection::with('user:id,name')->find($id)->makeHidden('api_token'));
    }

    /**
     * Search Jira Calculation Positions.
     *
     * This endpoint allows you to search for a String in all
     * stored Jira Connections.
     * @bodyParam text string Search String
     * @bodyParam components string[] Components
     */
    public function getSearchResult(Request $request)
    {
        $inputs = $request->input();

        $text = $inputs['text'];
        $components = $inputs['components'];

        $jiraConnections = JiraConnection::orderByDesc('updated_at')->get();
        $result = array();

        /*
         * TimeSpent Epics (aggregated timespent)
         */

        $jql = 'timespent = null AND ';
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

        foreach ($jiraConnections as $jiraConnection) {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($jiraConnection->email . ':' . $jiraConnection->api_token),
                'Content-Type' => 'application/json',
                'X-Atlassian-Token' => 'no-check',
            ])->get($jiraConnection->host. '/rest/api/2/search',[
                'jql' => $jql,
                'fields' => 'timespent,issueType,project,updated,status,assignee,progress,summary,aggregatetimespent'
            ]);
            $json = $response->json();

            foreach ($json['issues'] as $searchResult) {
                if($searchResult['fields']['aggregatetimespent'] !== null){
                    $searchResult['fields']['summary'] = $searchResult['fields']['summary'] . ' (Zeit aggregiert aus Subtasks)';
                    $searchResult['fields']['timespent'] = $searchResult['fields']['aggregatetimespent'];
                    $result[] = $searchResult;
                }
            }
        }

        /*
         * TimeSpent Tickets
         */

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
        }

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

    /**
     * Delete a Jira Connection.
     *
     * This endpoint allows you to delete a Jira Connection.
     */
    public function deleteConnection($id): \Illuminate\Http\JsonResponse
    {
        $con = JiraConnection::find($id);
        $con->delete();
        return response()->json('The connection was deleted',200);
    }
}
