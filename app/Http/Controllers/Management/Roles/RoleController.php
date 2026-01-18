<?php

namespace App\Http\Controllers\Management\Roles;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $excludedRoles = ['Super Admin'];
        $roles = Role::whereNotIn('name', $excludedRoles)->get();

        return view('yonetimsel-islemler.roller.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        
        // Get all permissions from Spatie
        $allPermissions = Permission::all();
        
        // Group permissions by category
        $permissionGroups = [
            'Performans Modülü' => [
                'Kategori Yönetimi',
                'Keyword Yönetimi',
                'Aktivite Yönetimi',
                'İstatistikler',
                'Bilgisayar Kullanıcıları',
            ],
            'Yönetimsel İşlemler' => [
                'Birim Yönetimi',
                'Ünvan Yönetimi',
                'Kullanıcı Yönetimi',
                'Rol Yönetimi',
            ],
        ];
        
        return view('yonetimsel-islemler.roller.add', compact('roles', 'permissionGroups', 'allPermissions'));
    }

     //Rol ekleme
     public function storeRole(Request $request)
     {
        
        $request->validate([

           
            'name' => 'required|string|max:255',
            
        ]);
        Role::updateOrCreate($request->except('_token'));
        return  redirect()->back()->with('message', 'Rol başarıyla eklendi');

     }
   
    //Role izinleri ekleme
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findById($request->role_id);
        
        // Checkbox'lardan gelen permission isimlerini topluyoruz
        $permissionNames = [];
        foreach ($request->except(['_token', 'role_id']) as $key => $value) {
            // Checkbox'lar 'on' değeri ile gelir
            if ($value === 'on') {
                // Underscore'ları boşluğa çeviriyoruz (view'dan böyle geliyor)
                $permissionNames[] = str_replace('_', ' ', $key);
            }
        }
        
        // syncPermissions: Var olanları tutar, olmayanları ekler, işaretlenmeyenleri kaldırır
        $role->syncPermissions($permissionNames);

        return redirect()->back()->with('message', 'Rol izinleri başarıyla güncellendi.');
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
    public function edit(Role $roller)
    {
        //dd($roller);
        return view('yonetimsel-islemler.roller.edit',compact('roller'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Role $roller, Request $request)
    {

        $request->validate([

           
            'name' => 'required|string|max:255',
            
        ]);

        //dd($request->name);

        $roller->update(['name'=>$request->name]);
        return  redirect()->route('roller.index')->with('message', 'Rol başarıyla güncellendi');
        
    }

 
    public function destroy(Role $roller)
    {
        $roller->delete();
        return  redirect()->back()->with('message', 'Rol başarıyla silindi');

    }

    public function getPermissions(Request $request, $roleId)
    {
        //return $roleId;
        //$role = Role::find($roleId);
        $role = Role::whereId($roleId)->first();


        //return $role;
        $permissions = $role->permissions->pluck('name');


        return response()->json($permissions);
    }
}
