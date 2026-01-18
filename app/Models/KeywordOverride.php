<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword_id',
        'category_id',
        'unit_id',
        'computer_user_id',
    ];

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function computerUser()
    {
        return $this->belongsTo(ComputerUser::class);
    }
}
