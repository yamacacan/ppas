<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordAlertException extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword_id',
        'computer_user_id',
        'unit_id',
    ];

    public function keyword()
    {
        return $this->belongsTo(CategoryKeyword::class, 'keyword_id');
    }

    public function computerUser()
    {
        return $this->belongsTo(ComputerUser::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
