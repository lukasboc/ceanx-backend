<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\CostEstimation;
use App\Models\EstimationPosition;
use App\Models\InvoiceInformation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Sodium\add;
/**
 * @group Settings
 *
 * Endpoints for handling Settings Requests.
 */
class SettingsController extends Controller
{
    /**
     * Toggle Registration.
     *
     * This endpoint allows you to disable and enable the Registration-feature.
     * @authenticated
     */
    public function toggleRegistration(Request $request)
    {
        $inputs = $request->input();

        $response = null;

        $current = DB::table('settings')
            ->where('setting', '=', "allow_registrations")
            ->first();


        if($current->boolean_value){
            DB::table('settings')
                ->where('setting', '=', "allow_registrations")
                ->update(["boolean_value" => false]);
        } else {
            DB::table('settings')
                ->where('setting', '=', "allow_registrations")
                ->update(["boolean_value" => true]);
        }

        $newCurrent = DB::table('settings')
            ->where('setting', '=', "allow_registrations")
            ->first();

        return $newCurrent->boolean_value;
    }

    /**
     * Get Registration Feature Status.
     *
     * This endpoint allows you to see if the Registration-feature is enabled.
     * If returns 0, the feature is disabled. If returns 1, the feature is enabled.
     */
    public function getRegisterAllowed(Request $request)
    {
        $response = DB::table('settings')
            ->where('setting', '=', "allow_registrations")
            ->first();
        return $response->boolean_value;
    }
}
