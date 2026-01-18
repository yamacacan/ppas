<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileName extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='file_name';
    protected $fillable=['title','order','file_category_id'];
    public function category()
    {
        return $this->belongsTo(FileCategory::class,'file_category_id','id');
    }
}
