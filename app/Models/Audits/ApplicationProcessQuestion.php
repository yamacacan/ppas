<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationProcessQuestion extends Model
{
   
    use HasFactory;
    public function applicationProcess() {
        return $this->hasOne(AuditApplicationProcess::class, 'question_id');
    }

}
