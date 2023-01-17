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

class SettingsController extends Controller
{
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

    public function getRegisterAllowed(Request $request)
    {
        $response = DB::table('settings')
            ->where('setting', '=', "allow_registrations")
            ->first();
        return $response->boolean_value;
    }
}
