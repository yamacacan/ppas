<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditApplicationProcess extends Model
{
    protected $guarded=[];
    use HasFactory;
    public function questions() {
        return $this->belongsTo(ApplicationProcessQuestion::class, 'question_id');
    }
}
