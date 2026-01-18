<?php

namespace App\Models\Audits;

use App\Models\EntitySubGroup;
use App\Models\Precautions\Precaution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditResponse extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded=[];

    public function subGroup() {
        return $this->belongsTo(EntitySubGroup::class, 'sub_group_id');
    }

    public function precaution() {
        return $this->belongsTo(Precaution::class, 'precaution_id');
    }

    public function findingCriticalityLevel() {
        return $this->belongsTo(FindingCriticalityLevel::class, 'finding_criticality_level', 'slug');
    }

    public function pasRelation() {
       
        return $this->belongsTo(PrecautionActivityStatus::class,'precaution_id','precaution_id');
        
    }

        
    
}
