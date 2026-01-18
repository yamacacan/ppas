<?php

namespace App\MYS;

class Helper
{
    public static function title($data)
    {
        return  \Transliterator::create('tr-title')->transliterate($data);
    }
}