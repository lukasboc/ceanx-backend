<?php

use App\Http\Controllers\ComponentsController;
use App\Http\Controllers\CostEstimationController;
use App\Http\Controllers\EstimationPositionController;
use App\Http\Controllers\JiraConnectionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TroiConnectionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/users/latest', [UserController::class, 'getLatestUsers']);

Route::middleware('auth:sanctum')->get('/cost_estimations/all', [CostEstimationController::class, 'getCostEstimations']);

Route::middleware('auth:sanctum')->get('/cost_estimations/latest', [CostEstimationController::class, 'getLatestCostEstimations']);

Route::middleware('auth:sanctum')->post('/cost_estimations/new', [CostEstimationController::class, 'createNewCostEstimation']);

Route::middleware('auth:sanctum')->post('/cost_estimations/edit', [CostEstimationController::class, 'editCostEstimation']);

Route::middleware('auth:sanctum')->post('/cost_estimations/duplicate', [CostEstimationController::class, 'duplicateCostEstimation']);

Route::middleware('auth:sanctum')->get('/cost_estimations/{id}', [CostEstimationController::class, 'getCostEstimationById']);

Route::middleware('auth:sanctum')->delete('/cost_estimations/{id}', [CostEstimationController::class, 'deleteCostEstimation']);

Route::middleware('auth:sanctum')->get('/cost_estimation_positions/cost_estimation/{id}', [EstimationPositionController::class, 'getCostEstimationById']);

Route::middleware('auth:sanctum')->post('/cost_estimation_positions/new', [EstimationPositionController::class, 'createNewCostEstimationPosition']);

Route::middleware('auth:sanctum')->post('/cost_estimation_positions/edit', [EstimationPositionController::class, 'editCostEstimationPosition']);

Route::middleware('auth:sanctum')->post('/cost_estimation_positions/search', [EstimationPositionController::class, 'getSearchResult']);

Route::middleware('auth:sanctum')->get('/cost_estimation_positions/{id}', [EstimationPositionController::class, 'getCostEstimationPositionById']);

Route::middleware('auth:sanctum')->delete('/cost_estimation_positions/{id}', [EstimationPositionController::class, 'deletePosition']);

Route::middleware('auth:sanctum')->post('/cost_estimation_positions/rename_component', [EstimationPositionController::class, 'renameComponent']);

Route::middleware('auth:sanctum')->post('/jira_connections/test', [JiraConnectionController::class, 'testJiraConnection']);

Route::middleware('auth:sanctum')->post('/jira_connections/new', [JiraConnectionController::class, 'createNewJiraConnection']);

Route::middleware('auth:sanctum')->get('/jira_connections/all', [JiraConnectionController::class, 'getJiraConnections']);

Route::middleware('auth:sanctum')->get('/jira_connections/{id}', [JiraConnectionController::class, 'getJiraConnectionById']);

Route::middleware('auth:sanctum')->delete('/jira_connections/{id}', [JiraConnectionController::class, 'deleteConnection']);

Route::middleware('auth:sanctum')->post('/jira_connections/search', [JiraConnectionController::class, 'getSearchResult']);

Route::middleware('auth:sanctum')->post('/troi_connections/test', [TroiConnectionController::class, 'testTroiConnection']);

Route::middleware('auth:sanctum')->post('/troi_connections/new', [TroiConnectionController::class, 'createNewTroiConnection']);

Route::middleware('auth:sanctum')->get('/troi_connections/all', [TroiConnectionController::class, 'getTroiConnections']);

Route::middleware('auth:sanctum')->get('/troi_connections/{id}', [TroiConnectionController::class, 'getTroiConnectionById']);

Route::middleware('auth:sanctum')->delete('/troi_connections/{id}', [TroiConnectionController::class, 'deleteConnection']);

Route::middleware('auth:sanctum')->post('/troi_connections/search', [TroiConnectionController::class, 'getSearchResult']);

Route::middleware('auth:sanctum')->get('/components/all', [ComponentsController::class, 'getComponents']);

Route::middleware('auth:sanctum')->post('/components/new', [ComponentsController::class, 'createNewComponent']);

Route::middleware('auth:sanctum')->delete('/components/{id}', [ComponentsController::class, 'deleteComponent']);

Route::middleware('auth:sanctum')->post('/setting/toggle_registration', [SettingsController::class, 'toggleRegistration']);

Route::get('/setting/register_allowed', [SettingsController::class, 'getRegisterAllowed']);
