<?php

namespace App\Models\itil\ServiceRequests;

use App\Models\EntitySubGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceRequest extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function subGroup() {
        return $this->belongsTo(EntitySubGroup::class, 'service_id');
    }

   
}
