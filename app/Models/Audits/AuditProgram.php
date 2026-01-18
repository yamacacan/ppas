<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditProgram extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function auditors() {
        return $this->belongsTo(Auditor::class, 'auditor_id');
    }

    public function personnels() {
        return $this->belongsTo(InformationReceivedPersonnel::class, 'personnel_id');
    }
}
