<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkForm extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function consitituentAuditor(){
        return $this->BelongsTo(Auditor::class,'constituent_auditor_id');
    }

    public function reviewingAuditor(){
        return $this->BelongsTo(Auditor::class,'reviewing_auditor_id');
    }
}
