<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['firstName'],
            'last_name' => $input['lastName'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // Rol atama işlemi

        $role = Role::where('name', 'Anket Kullanıcısı')->first();
        if ($role) {
            $user->assignRole($role);
        } else {
            // Rol bulunamazsa hata veya uyarı loglayabilirsiniz
            Log::warning("Rol bulunamadı: Anket Kullanıcısı");
        }
      

        return $user;
    }
}
