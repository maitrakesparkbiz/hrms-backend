<?php

namespace App;

class Helper
{
    public static function GetAssetPath($path)
    {
        return asset($path) . '?v=' . time();
    }
}

