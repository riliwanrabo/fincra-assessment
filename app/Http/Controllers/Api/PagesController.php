<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index()
    {
        return response()->ok([
            'message' => 'Hello!, Bienvenue!, Ekáàbọ́'
        ]);
    }

    public function health()
    {
        return response()->ok([
            'message' => "Hello!, Bienvenue!, Ekáàbọ́.",
            'environment' => "Application is " . env('APP_ENV'),
            'database' => env('DB_CONNECTION')
        ]);
    }
}
