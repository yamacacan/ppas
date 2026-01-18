<?php

namespace App\Models\Audits;

use App\Models\Precautions\Precaution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditQuestion extends Model
{
    use HasFactory;
    protected $guarded=[];

    //relation with precaution
    public function precaution(){
        return $this->belongsTo(Precaution::class);
    }
}
