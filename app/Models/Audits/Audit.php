<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Audit extends Model
{
    protected $guarded=[];
    use HasFactory,SoftDeletes;

    public function AuditorAssignmentTypes() {
        return $this->belongsTo(AuditorAssignmentType::class, 'auditor_type');
    }
}
