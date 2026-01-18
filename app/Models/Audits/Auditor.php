<?php

namespace App\Models\Audits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auditor extends Model
{
    protected $guarded=[];
    use HasFactory,SoftDeletes;
}
