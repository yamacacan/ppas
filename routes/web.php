<?php

use App\Models\User;
use LdapRecord\Ldap;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\DashboardController;



use LdapRecord\Models\ActiveDirectory\User as LdapUser;
use App\Http\Controllers\Audits\AuditPersonnelController;
use App\Http\Controllers\Management\Roles\RoleController;
use App\Http\Controllers\Management\Users\UserController;
use App\Http\Controllers\Precautions\PrecautionController;

use App\Http\Controllers\Audits\PrecautionActivityStatusController;
use App\Http\Controllers\Precautions\PrecautionMainTitleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('/');

//Route::view('index', 'index')->name('index')->middleware(['auth']);

Route::middleware(['auth', 'verified'])->group(function () {

    //Kullanıcı işlemleri
    Route::resource('kullanicilar', UserController::class)->middleware('role:Admin|Super Admin');

    //Role işlemleri
    route::resource('roller', RoleController::class)->middleware('role:Admin|Super Admin');
    Route::get('/roles/{id}/permissions', [RoleController::class, 'getPermissions'])->middleware('role:Admin|Super Admin');
    Route::post('/add-role', [RoleController::class, 'storeRole'])->name('roller.storeRole');
    
    // Firm Settings
    Route::get('/firm-settings', [\App\Http\Controllers\Management\FirmSettingsController::class, 'edit'])
         ->name('firm-settings.edit')
         ->middleware('permission:Firma Ayarları');
    Route::post('/firm-settings', [\App\Http\Controllers\Management\FirmSettingsController::class, 'update'])
         ->name('firm-settings.update')
         ->middleware('permission:Firma Ayarları');

  




    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Performance Agent Routes
    Route::prefix('performance')->group(function () {
        // Categories
        Route::resource('categories', \App\Http\Controllers\Web\CategoryViewController::class)
            ->middleware('permission:Kategori Yönetimi');
        
        // Keywords  
        Route::resource('keywords', \App\Http\Controllers\Web\KeywordViewController::class)
            ->middleware('permission:Keyword Yönetimi');
        Route::get('keywords-test', [\App\Http\Controllers\Web\KeywordViewController::class, 'test'])
            ->name('keywords.test')->middleware('permission:Keyword Yönetimi');
        Route::get('keywords-import', [\App\Http\Controllers\Web\KeywordViewController::class, 'import'])
            ->name('keywords.import')->middleware('permission:Keyword Yönetimi');
        Route::post('keywords-import', [\App\Http\Controllers\Web\KeywordViewController::class, 'importPost'])
            ->name('keywords.import.post')->middleware('permission:Keyword Yönetimi');
        
        // Keyword Overrides
        Route::post('keywords/{id}/overrides', [\App\Http\Controllers\Web\KeywordViewController::class, 'storeOverride'])
            ->name('keywords.overrides.store')->middleware('permission:Keyword Yönetimi');
        Route::delete('keywords/overrides/{id}', [\App\Http\Controllers\Web\KeywordViewController::class, 'destroyOverride'])
            ->name('keywords.overrides.destroy')->middleware('permission:Keyword Yönetimi');

        // Keyword Alert Exceptions
        Route::post('keywords/{id}/alert-exceptions', [\App\Http\Controllers\Web\KeywordViewController::class, 'storeAlertException'])
            ->name('keywords.alert-exceptions.store')->middleware('permission:Keyword Yönetimi');
        Route::delete('keywords/alert-exceptions/{id}', [\App\Http\Controllers\Web\KeywordViewController::class, 'destroyAlertException'])
            ->name('keywords.alert-exceptions.destroy')->middleware('permission:Keyword Yönetimi');
        
        // Activities
        Route::resource('activities', \App\Http\Controllers\Web\ActivityViewController::class)
            ->middleware('permission:Aktivite Yönetimi');
        Route::get('activities-tagged', [\App\Http\Controllers\Web\ActivityViewController::class, 'tagged'])
            ->name('activities.tagged')->middleware('permission:Aktivite Yönetimi');
        Route::get('activities-untagged', [\App\Http\Controllers\Web\ActivityViewController::class, 'untagged'])
            ->name('activities.untagged')->middleware('permission:Aktivite Yönetimi');
        Route::get('activities-auto-tag', [\App\Http\Controllers\Web\ActivityViewController::class, 'autoTagPage'])
            ->name('activities.auto-tag')->middleware('permission:Aktivite Yönetimi');
        Route::post('activities-auto-tag-run', [\App\Http\Controllers\Web\ActivityViewController::class, 'autoTagRun'])
            ->name('activities.auto-tag.run')->middleware('permission:Aktivite Yönetimi');
        
        // Statistics
        Route::get('statistics', [\App\Http\Controllers\Web\StatisticsViewController::class, 'index'])
            ->name('statistics.index')->middleware('permission:İstatistikler');
        Route::get('statistics-tagging-rate', [\App\Http\Controllers\Web\StatisticsViewController::class, 'taggingRate'])
            ->name('statistics.tagging-rate')->middleware('permission:İstatistikler');
        Route::get('statistics-time-distribution', [\App\Http\Controllers\Web\StatisticsViewController::class, 'timeDistribution'])
            ->name('statistics.time-distribution')->middleware('permission:İstatistikler');
        Route::get('statistics-work-other', [\App\Http\Controllers\Web\StatisticsViewController::class, 'workOther'])
            ->name('statistics.work-other')->middleware('permission:İstatistikler');
        
        // Computer Users
        Route::resource('computer-users', \App\Http\Controllers\Web\ComputerUserController::class)
            ->middleware('permission:Bilgisayar Kullanıcıları');

        // Computers (Hardware & Apps)
        Route::resource('computers', \App\Http\Controllers\ComputerController::class)
            ->only(['index', 'show'])
            ->middleware('permission:Bilgisayar Kullanıcıları');

        // Unit Statistics
        Route::resource('unit-statistics', \App\Http\Controllers\Web\UnitStatisticsController::class)
            ->only(['index', 'show'])
            ->middleware('permission:İstatistikler');
    });
    
    Route::resource('birim', UnitController::class)->middleware('permission:Birim Yönetimi');
    Route::resource('unvan', TitleController::class)->middleware('permission:Ünvan Yönetimi');
  
    //Ana gruba göre varlık grubunu almak için jquery ile

    //Kullanıcı bilgilerin gelmesi
    Route::get('/get-user', [UserController::class, 'getUser']);











    //Notify
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notify/checkread',[\App\Http\Controllers\NotificationController::class,'checkread'])->name('notify.checkread'); // Keeping existing for backward compatibility if needed

    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});




// Route::view('login', 'authentication.login')->name('login');

Route::prefix('authentication')->group(function () {
    //Route::view('login', 'authentication.login')->name('login');
    // Route::view('register', 'authentication.register')->name('register');
});

// Route::get('/test-ldap-user', function () {
//     // Kullanıcıyı LDAP içinde arayın
//     $user = \LdapRecord\Models\Entry::where('uid', '=', 'riemann')->first();

//     if ($user) {
//         $email = $user->getFirstAttribute('mail'); // Kullanıcının mail özelliğini alın
//         return 'LDAP kullanıcı bulundu: ' . $user->getFirstAttribute('cn') . '<br>E-posta: ' . $email;
//     }

//     return 'LDAP kullanıcısı bulunamadı2';
// });

Route::get('/test-ldap-user', function () {
    // Kullanıcıyı LDAP içinde arayın
    $user = \LdapRecord\Models\Entry::where('uid', '=', 'riemann')->first();

    if ($user) {
        $email = $user->getFirstAttribute('mail'); // Kullanıcının mail özelliğini alın
        //return 'LDAP kullanıcı bulundu: ' . $user->getFirstAttribute('cn') . '<br>E-posta: ' . $email;
    } else {
        return 'LDAP kullanıcısı bulunamadı';
    }

    $credentials = [
        'mail' => $email, // Test kullanıcı e-postası
        'password' => 'password' // Test kullanıcı şifresi
    ];

    Log::info('LDAP kimlik doğrulama başlıyor. Giriş bilgileri: ', $credentials);

    try {
        if (Auth::guard('ldap')->attempt($credentials)) {
            $ldapUser = Auth::guard('ldap')->user();
            return 'LDAP kullanıcı doğrulandı: ' . $ldapUser->getFirstAttribute('cn') . '<br>E-posta: ' . $ldapUser->getFirstAttribute('mail');
        } else {
            Log::error('LDAP doğrulama başarısız. Giriş bilgileri: ', $credentials);
            $ldap = app(Ldap::class);
            $detailedError = $ldap->getDetailedError();
            if ($detailedError) {
                Log::error('LDAP Hata: ' . json_encode($detailedError));
                return 'LDAP doğrulama başarısız: ' . json_encode($detailedError);
            }
            return 'LDAP doğrulama başarısız';
        }
    } catch (\Exception $e) {
        Log::error('LDAP kimlik doğrulama sırasında bir hata oluştu: ' . $e->getMessage());
        return 'LDAP kimlik doğrulama sırasında bir hata oluştu: ' . $e->getMessage();
    }
});

