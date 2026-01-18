<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\itil\ServiceRequests\ServiceRequestController;

Route::middleware(['auth','verified'])->group(function(){



        Route::resource('service_requests',ServiceRequestController::class)->middleware('permission:Varlık Grubu İşlemleri')->middleware('permission:Servis İstekleri');
        Route::post('service-request-response/{id}', [ServiceRequestController::class,'storeResponse'])->name('service-request-response.storeResponse');


});
