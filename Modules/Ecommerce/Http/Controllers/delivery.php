<?php

namespace Modules\Ecommerce\Http\Controllers;

use Modules\Ecommerce\Entities\Blog;
use Illuminate\Http\Request;
use Str;
use Session;
use DB;

class delivery extends Controller
{

    public function index()
    {
        dd('asdad');
        $divisions = DB::table('countries')->where('status', 1)->select('id', 'name')->get();
        return response()->json($divisions);
    }

    public function getDivisions()
    {
        dd('asdeeead');
        $divisions = DB::table('countries')->where('status', 1)->select('id', 'name')->get();
        dd('aa',$divisions);
        return response()->json($divisions);
    }

    public function getDistricts($division_id)
    {
        // dd('adfayyyyyyysd');
        $districts = DB::table('states')->where('country_id', $division_id)
            ->where('status', 1)
            ->select('id', 'name')
            ->get();
        return response()->json($districts);
    }

    public function getUpazillas($district_id)
    {
        $upazillas = DB::table('cities')->where('state_id', $district_id)
            ->where('status', 1)
            ->select('id', 'name', 'cost')
            ->get();
        return response()->json($upazillas);
    }


}
