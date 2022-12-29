<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Fincra\FincraService;
use Illuminate\Support\Facades\Http;

class ExperimentController extends Controller
{

    private string $businessId;

    public function __construct()
    {
        $this->businessId = $this->fetchBusinessId();
    }

    public function fetchBanks()
    {
        return (new FincraService)->fetchBanks();
    }

    public function fetchBusinessId()
    {
        return (new FincraService)->getBusinessId();
    }

    // the actions below are for support purposes

    public function createSubAccount(FincraService $fincraService)
    {
        return  $fincraService->createSubAccount();
    }

    public function fetchSubAccounts(FincraService $fincraService)
    {
        return  $fincraService->fetchSubAccounts();
    }


    public function createVirtualAccount(FincraService $fincraService, Request $request)
    {
        $subAccountId = request()->query('subAccountId');

        $payload = $request->except('subAccountId');

        return  $fincraService->createVirtualAccount($subAccountId, $payload);
    }

    private function requestBody()
    {
        return;
    }
}
