<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name','parent_id'];

    public function parentUnit() {
        return $this->belongsTo($this, 'parent_id');
    }

    public function users()
    {
        // Unit -> UserDetail -> User
        return $this->hasManyThrough(User::class, UserDetail::class, 'unit_id', 'id', 'id', 'user_id');
    }

    public function computerUsers()
    {
        return $this->hasMany(ComputerUser::class);
    }
}
