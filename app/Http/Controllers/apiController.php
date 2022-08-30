<?php

namespace App\Http\Controllers;

use http\Env\Request;
use Illuminate\Support\Facades\Artisan;

class apiController extends Controller
{
    public function configCache()
    {
        Artisan::call('config:cache');
        return 'done';
    }
}
