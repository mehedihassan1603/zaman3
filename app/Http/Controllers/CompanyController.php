<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Company;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class CompanyController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        $companies = Company::where('is_active', true)->get();
        $groups = Group::where('is_active',true)->get();
        $areas = Area::where('is_active',true)->get();
        return view('backend.company.index', compact('companies','groups','areas'));
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
        $data['group_id'] = $request->input('group_id');
        $data['area_id'] = $request->input('area_id');
        $data['head_office_address'] = $request->input('head_office_address');
        $data['factory_office_address'] = $request->input('factory_office_address');
        Company::create($data);
        return redirect('companies')->with('message', 'Company created successfully');
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

    public function getAddress($id)
    {
        $company = Company::findOrFail($id);

        return response()->json([
            'head_office_address' => $company->head_office_address,
            'factory_office_address' => $company->factory_office_address,
        ]);
    }





}
