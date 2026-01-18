<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileCategory extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='file_category';
    protected $fillable=['text','prefix'];
}
