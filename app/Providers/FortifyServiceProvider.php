<?php

namespace App\Providers;

use App\Models\User;
use LdapRecord\Ldap;
use App\Models\UserDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Fortify::loginView(function () {

            return view('authentication.login');
        });

        Fortify::registerView(function () {
            return view('authentication.register');
        });

        Fortify::requestPasswordResetLinkView(function () {

            return view('authentication.password.request');
        });

        Fortify::resetPasswordView(function ($request) {
            return view('authentication.password.reset-password');
        });

        Fortify::verifyEmailView(function () {

            return view('authentication.verify-email');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

                
          
    Fortify::authenticateUsing(function (Request $request) {
        $credentials = $request->only('email', 'password');
    
        // LDAP kullanıcı doğrulama
        try {
            Log::info('LDAP kimlik doğrulama başlıyor. Giriş bilgileri: ', $credentials);
    
            // LDAP kullanıcıyı bul
            $ldapUser = \LdapRecord\Models\Entry::where('mail', '=', $credentials['email'])->first();
    
            if ($ldapUser) {
                Log::info('LDAP kullanıcı bulundu: ' . $ldapUser->getFirstAttribute('cn'));
    
                // LDAP kimlik doğrulama
                $ldapConnection = app(Ldap::class);
                $ldapConnection->connect(env('LDAP_HOST'), env('LDAP_PORT'));
                $ldapConnection->bind($ldapUser->getDn(), $credentials['password']);
    
                if ($ldapConnection->isBound()) {
                    Log::info('LDAP kimlik doğrulama başarılı.');
    
                    // LDAP kullanıcı bilgilerini al
                    $email = $ldapUser->getFirstAttribute('mail');
                    $name = $ldapUser->getFirstAttribute('cn');
                    $lastName = $ldapUser->getFirstAttribute('sn'); // Soyadı (surname)
    
                    Log::info('LDAP User: ' . $name . ' - ' . $email);
    
                    // Kullanıcıyı `users` tablosuna kaydet
                    $user = User::firstOrCreate([
                        'email' => $email,
                    ], [
                        'name' => $name,
                        'last_name' => $lastName, // Soyadı alanını ekleyin
                        'password' => bcrypt('default-password'), // Varsayılan şifre
                    ]);

                    $user->assignRole('admin');
                    $user->markEmailAsVerified();

                    // Kullanıcıyı giriş yapmış olarak göster
                    Auth::login($user);
                    return $user;
                } else {
                    Log::error('LDAP kimlik doğrulama başarısız. Şifre yanlış.');
                }
            } else {
                Log::error('LDAP kullanıcı bulunamadı.');
            }
        } catch (\Exception $e) {
            Log::error('LDAP kimlik doğrulama sırasında bir hata oluştu: ' . $e->getMessage());
        }
    
        // Veritabanı kullanıcı doğrulama
        if (Auth::guard('web')->attempt($credentials)) {
            return Auth::guard('web')->user();
        }
    
        // Her iki doğrulama da başarısızsa, hata fırlat
        throw ValidationException::withMessages([
            Fortify::username() => __('These credentials do not match our records.'),
        ]);
    
        return null;
    });
    }
}
