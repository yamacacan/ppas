<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputerUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'motherboard_uuid',
        'hostname',
        'name',
        'unit_id',
    ];

    /**
     * Kullanıcının aktiviteleri
     */
    public function activities()
    {
        return $this->hasMany(Activity::class, 'username', 'username')
            ->where('motherboard_uuid', $this->motherboard_uuid);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function overrides()
    {
        return $this->hasMany(KeywordOverride::class);
    }
}
