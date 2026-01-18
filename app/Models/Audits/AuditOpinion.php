<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditOpinion extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function audits() {
        return $this->belongsTo(Audit::class, 'audit_id');
    }
}
