<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $units = Unit::orderBy('name')->get();
        //dd($units);
        return  view('birim.index',compact('units'));
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $units = Unit::orderBy('name')->get();
        return  view('birim.add',compact('units'));
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
        Unit::create($request->only('parent_id','name'));
        return redirect()->route('birim.index')->with('message',"Birim başarılı bir şekilde eklendi");


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
        $unit=Unit::findOrFail($id);
        $units = Unit::orderBy('name')->where('id', '!=', $id)->get();

           

        //dd($units->p);
        return view('birim.edit',compact('unit','units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'=>'required',
            
        ]);
        
        $unit=Unit::findOrFail($id);
        $unit->update($request->only('parent_id','name'));

        return redirect()->route('birim.index')->with('message',"Birim başarı ile güncellendi");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

       
        $unit=Unit::findOrFail($id);

        try {
            $unit->delete();
        } catch (\Exception $e) {
            return redirect()->route('birim.index')->with('error',"Birim silinemedi. Bu birime bağlı başka bir birim bulunmaktadır.");
        }
        $unit->delete();

        return redirect()->route('birim.index')->with('message',"Birim başarılı bir şekilde silindi");
    }
}
