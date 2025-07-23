<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;



class GroupController extends Controller
{
    public function index() {

        $role = Role::find(Auth::user()->role_id);
//        if($role->hasPermissionTo('area')) {
        $groups = Group::where('is_active', true)->with('area')->get();
        $areas = Area::where('is_active', true)->get();
        return view('backend.group.index', compact('groups','areas'));
//        }
//        else
//            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data['name'] = $request->input('name');
        $data['area_name'] = $request->input('area_name')??null;
        Group::create($data);
        return redirect('groups')->with('message', 'Group created successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
