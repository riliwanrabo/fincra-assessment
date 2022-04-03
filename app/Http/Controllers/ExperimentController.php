<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Fincra\FincraService;

class ExperimentController extends Controller
{
    public function fetchBanks()
    {
        return (new FincraService)->fetchBanks();
    }

    public function fetchBusinessId()
    {
        return (new FincraService)->getBusinessId();
    }
}
