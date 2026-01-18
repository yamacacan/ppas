<?php

namespace App\Http\Controllers;

use App\Models\Title;
use Illuminate\Http\Request;

class TitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        $titles = Title::orderBy('name')->get();
        //dd($titles);
        return  view('unvan.index',compact('titles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $titles = Title::orderBy('name')->get();
        return  view('unvan.add',compact('titles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            
        ]);
        
        //dd($request->only('unit','parent_unit'));
        Title::create($request->only('name'));
        return redirect()->route('unvan.index')->with('message',"Ünvan başarılı bir şekilde eklendi");
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
        $title=Title::findOrFail($id);

        return view('unvan.edit',compact('title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'=>'required',
            
        ]);
        
        $title=Title::findOrFail($id);
        $title->update($request->only('name'));

        return redirect()->route('unvan.index')->with('message',"Ünvan başarı ile güncellendi");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $title=Title::findOrFail($id);
        $title->delete();

        return redirect()->route('unvan.index')->with('message',"Ünvan başarılı bir şekilde silindi");
    }
}
