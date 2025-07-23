<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Terms;
use Illuminate\Http\Request;

class TermSettingController extends Controller
{
    public function index()
    {
        $terms = Terms::all();
        return view('backend.terms.index',compact('terms'));
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
        Terms::create($data);
        return redirect('terms')->with('message', 'Terms and Conditions created successfully');
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
