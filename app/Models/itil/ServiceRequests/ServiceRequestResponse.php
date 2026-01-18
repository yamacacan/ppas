<?php

namespace App\Models\itil\ServiceRequests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequestResponse extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function serviceRequest() {
        return $this->hasMany(ServiceRequest::class, 'service_request_id');
    }
}
