<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class File extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='file';
    protected $fillable=['url','file_name_id',	'status',	'uploaded_date',	'uploaded_user',	'revision_date',	'revision_order','revision_explain'];

    public function file_name()
    {
        return $this->belongsTo(FileName::class,'file_name_id','id');
    }
}
