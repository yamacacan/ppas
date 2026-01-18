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

        $user->roles()->sync($request->role_id);


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

      

        return view('yonetimsel-islemler.kullanicilar.edit',compact('user','units','titles','roles'));
    }


    public function update(Request $request, string $id)
    {

        $user=User::find($id);
        //dd( $user->roles[0]->id);
        $user->roles()->sync($request->role_id);


        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id),
            ],
            'phone' => 'required|digits:11',
            
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8',
            ]);
        }

        $userData=[

            'name'=>$request->name,
            'last_name'=>$request->last_name,
            'email'=>$request->email,
            


        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        User::whereId($id)->update($userData);

        $userDetailsData=[

            'unit_id'=>$request->unit_id,
            'title_id'=>$request->title_id,
            'phone'=>$request->phone

        ];

        UserDetail::where('user_id',$id)->update($userDetailsData);

       
        return redirect()->route('kullanicilar.index')->with('message','Kullanıcı başarılı bir şekilde güncellendi');

       
        //dd($request->all());
    }


    public function destroy(User $kullanicilar)
    {
        $kullanicilar->delete();
        return redirect()->route('kullanicilar.index')->with('message','Kullanıcı başarılı bir şekilde silindi');
    }

    public function getUser(Request $request){

        $userId=$request->userId;
        $user=User::whereId($userId)->select('name','last_name','email')->first();
        return response()->json($user);


    }
}
