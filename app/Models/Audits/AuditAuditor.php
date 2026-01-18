<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditAuditor extends Model
{
    protected $guarded=[];
    use HasFactory,SoftDeletes;

    public function audits() {
        return $this->belongsTo(Audit::class, 'audit_id');
    }

    public function auditors() {
        return $this->belongsTo(Auditor::class, 'auditor_id');
    }

    public function auditorRoles() {
        return $this->belongsTo(AuditorRole::class, 'auditor_role_id');
    }

    public function AuditorAssignmentTypes() {
        return $this->belongsTo(AuditorAssignmentType::class, 'auditor_assignment_type_id');
    }
}
