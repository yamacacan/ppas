<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\EntityImport;
use Maatwebsite\Excel\Facades\Excel;

class EntityFromFileController extends Controller
{
    public function index(){
        return view('varlik.entity_from_file');
    }

    public function import(Request $request){
       
        $request->validate([
            'entity_file'=>'required',
            
        ]);
        

        Excel::import(new EntityImport,$request->file('entity_file')); 
        return back();
    }
}
