<?php

namespace App\Models\Audits;

use App\Models\Entity;
use App\Models\EntitySubGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditEntity extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded=[];

    public function subGroup() {
        return $this->belongsTo(EntitySubGroup::class, 'entity_subgroup_id');
    }

    public function entity() {
        return $this->belongsTo(Entity::class, 'entity_subgroup_id');
    }

    public function audits() {
        return $this->belongsTo(Audit::class, 'audit_id');
    }
}
