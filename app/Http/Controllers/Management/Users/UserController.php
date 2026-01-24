<?php

namespace App\Http\Controllers\Management\Users;

use App\Models\Unit;
use App\Models\User;
use App\Models\Title;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{

    public function index()
    {
        $users=User::with('details')->get();

      
        return view('yonetimsel-islemler.kullanicilar.index',compact('users'));
    }


    public function create()
    {
        $units = Unit::all();
        $titles = Title::all();
        $roles= Role::all();

        $role= Role::findByName('Super Admin');

      //dd($role);
        return view('yonetimsel-islemler.kullanicilar.add', compact('units', 'titles','roles'));
    }


    public function store(Request $request)
    {
        $request->validate([

            'title_id'=>'required',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mail' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|digits:11',
            'password' => 'required|string|min:8'
        ]);

        //dd($request->all());

        $user=User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->mail,
            'password' => Hash::make($request->password),
        ]);

        // try {
        //     $user->sendEmailVerificationNotification(); 
        // } catch (\Throwable $th) {
        //     Log::error("E-posta doğrulama hatası: " . $th->getMessage());
        //     $error="E-posta doğrulama hatası";
        //     return view('errors.custom_error',compact('error'));
        // }
            
        $user->markEmailAsVerified();

        $role_ids = $request->role_id;
        // Super Admin olmayanlar Super Admin rolü atayamaz
        if (!auth()->user()->hasRole('Super Admin')) {
            $superAdminRole = Role::where('name', 'Super Admin')->first();
            if ($superAdminRole && in_array($superAdminRole->id, (array)$role_ids)) {
                return redirect()->back()->with('error', 'Super Admin rolü atama yetkiniz yok.');
            }
        }

        $user->roles()->sync($role_ids);


        UserDetail::create([
            'user_id'=>$user->id,
            'unit_id' => $request->unit_id,
            'title_id' => $request->title_id,
            'phone' => $request->phone
        ]);

        

        return redirect()->route('kullanicilar.index')->with('message','Kullanıcı başarılı bir şekilde oluşturuldu');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(User $kullanicilar)
    {

        
        $user=$kullanicilar;
        $units = Unit::get();
        $titles = Title::all();
        $roles= Role::all();

      
        if (auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Super Admin')) {
            if ($kullanicilar->hasAnyRole(['Admin', 'Super Admin'])) {
                return redirect()->route('kullanicilar.index')->with('error', 'Diğer admin kullanıcılarını düzenleme yetkiniz yok.');
            }
        }

        return view('yonetimsel-islemler.kullanicilar.edit',compact('user','units','titles','roles'));
    }


    public function update(Request $request, string $id)
    {
        $user = User::with('details')->findOrFail($id);

        if (auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Super Admin')) {
            if ($user->hasAnyRole(['Admin', 'Super Admin'])) {
                return redirect()->route('kullanicilar.index')->with('error', 'Diğer admin kullanıcılarını güncelleme yetkiniz yok.');
            }
        }

        $role_ids = $request->role_id;
        // Super Admin olmayanlar Super Admin rolü atayamaz veya kaldıramaz (başkasından)
        if (!auth()->user()->hasRole('Super Admin')) {
            $superAdminRole = Role::where('name', 'Super Admin')->first();
            if ($superAdminRole && in_array($superAdminRole->id, (array)$role_ids)) {
                return redirect()->back()->with('error', 'Super Admin rolü atama yetkiniz yok.');
            }
        }

        $user->roles()->sync($role_ids);

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mail' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'phone' => 'required|digits:11',
            'title_id' => 'required',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8',
            ]);
        }

        $userData=[
            'name'=>$request->name,
            'last_name'=>$request->last_name,
            'email'=>$request->mail,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        $user->details()->updateOrCreate(
            ['user_id' => $id],
            [
                'unit_id'=>$request->unit_id,
                'title_id'=>$request->title_id,
                'phone'=>$request->phone
            ]
        );

       
        return redirect()->route('kullanicilar.index')->with('message','Kullanıcı başarılı bir şekilde güncellendi');

       
        //dd($request->all());
    }


    public function destroy(User $kullanicilar)
    {
        if (auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Super Admin')) {
            if ($kullanicilar->hasAnyRole(['Admin', 'Super Admin'])) {
                return redirect()->route('kullanicilar.index')->with('error', 'Diğer admin kullanıcılarını silme yetkiniz yok.');
            }
        }

        $kullanicilar->delete();
        return redirect()->route('kullanicilar.index')->with('message','Kullanıcı başarılı bir şekilde silindi');
    }

    public function getUser(Request $request){

        $userId=$request->userId;
        $user=User::whereId($userId)->select('name','last_name','email')->first();
        return response()->json($user);


    }
}
