<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditPersonnel extends Model
{
    protected $guarded=[];
    use HasFactory,SoftDeletes;

    public function audits() {
        return $this->belongsTo(Audit::class, 'audit_id');
    }

    public function auditIRPersonnels() {
        return $this->belongsTo(InformationReceivedPersonnel::class, 'personnel_id');
    }
}
