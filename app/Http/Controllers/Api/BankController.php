<?php

namespace App\Http\Controllers\Api;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankController extends Controller
{
    public function index()
    {
        return cache()->rememberForever('banks', function () {
            return  Bank::orderBy('name')->get();
        });
    }
}
