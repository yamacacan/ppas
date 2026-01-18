<?php

namespace App\Models\Audits;

use App\Models\EntitySubGroup;
use App\Models\GapAnalysis;
use App\Models\Precautions\Precaution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrecautionActivityStatus extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function precaution() {
        return $this->belongsTo(Precaution::class, 'precaution_id');
    }

    public function subGroup() {
        return $this->belongsTo(EntitySubGroup::class, 'subgroup_id');
    }


}
