<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Area;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
//        if($role->hasPermissionTo('area')) {
            $areas = Area::where('is_active', true)->get();
            $groups = Group::where('is_active', true)->get();
            $companies = Company::where('is_active', true)->get();
            return view('backend.area.index', compact('areas','groups','companies'));
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
        Area::create($data);
        return redirect('areas')->with('message', 'Area created successfully');
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
        $this->validate($request,[
            'name' => [
                'max:255',
                Rule::unique('areas')->ignore($request->area_id)->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
        ]);

        $data = $request->all();
        $area_data = Area::find($data['area_id']);
        $area_data->update($data);
        return redirect('areas')->with('message', 'Area updated successfully');
    }


    public function deleteBySelection(Request $request)
    {
        $department_id = $request['departmentIdArray'];
        foreach ($department_id as $id) {
            $lims_department_data = Department::find($id);
            $lims_department_data->is_active = false;
            $lims_department_data->save();
        }
        return 'Department deleted successfully!';
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $area = Area::find($id);
        $area->is_active = false;
        $area->save();
        return redirect('areas')->with('message', 'Area deleted successfully');
    }
}
