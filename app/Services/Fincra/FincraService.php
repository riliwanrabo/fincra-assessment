<?php

namespace App\Services\Fincra;

use RuntimeException;
use Illuminate\Support\Facades\Http;
use App\Exceptions\MisMatchException;
use App\Models\BankAccount;

class FincraService
{
    protected string $baseUrl;
    protected  $businessId;
    private const BUSINESS_CACHE_KEY = 'fincra_business_id';

    public function __construct()
    {
        $this->baseUrl = env('FINCRA_BASE_URL');
    }

    public function getBusinessId()
    {
        $businessId = cache()->get(self::BUSINESS_CACHE_KEY);

        if (!$businessId) {
            $endpoint = $this->baseUrl . '/profile/merchants/me';
            $request = $this->http()->get($endpoint);

            if ($request->ok() && $request->object()?->success === true) {
                $businessId =  $request->object()?->data->business->id;
                return cache()->rememberForever(self::BUSINESS_CACHE_KEY, fn () => $businessId);
            }
        }

        return $businessId;
    }


    public function fetchBanks()
    {
        $endpoint = $this->baseUrl . '/core/banks?currency=NGN';
        $request = $this->http()->get($endpoint);

        if ($request->ok() && $request->object()?->success === true) {
            return $request->object()?->data;
        } else {
            throw new RuntimeException("Could not retreive banks");
        }
    }

    public function resolveAccount(string $accountNumber, string $bankCode)
    {
        $endpoint = $this->baseUrl . '/core/accounts/resolve';

        $requestBody =  [
            'accountNumber' => $accountNumber,
            'bankCode' => $bankCode
        ];

        $request = $this->http()->post($endpoint, $requestBody);

        if ($request->ok() && $request->object()?->success === true) {
            return $request->object()?->data;
        } else {
            throw new RuntimeException("Could not resolve account details");
        }
    }

    public function transfer(string $reference, string $accountNumber, string $bankCode, float $amount, BankAccount $bankAccount, $description = "Funds Transfer")
    {
        $endpoint = $this->baseUrl . '/disbursements/payouts';
        $request = $this->http()->post(
            $endpoint,
            $this->transferRequestBody(
                $reference,
                $amount,
                $description,
                $bankAccount
            )
        );

        if ($request->ok() && $request->object()?->success === true) {
            return $request->object()?->data;
        } else {
            throw new RuntimeException("Bank transfer failed to process");
        }
    }

    public function transferRequestBody($reference, $amount, $description, BankAccount $bankAccount)
    {
        return [
            'sourceCurrency' => 'NGN',
            'destinationCurrency' => 'NGN',
            'amount' => $amount,
            'business' => $this->getBusinessId(),
            'description' => $description,
            'customerReference' => $reference,
            'beneficiary' => [
                'firstName' => 'DEMO',
                'type' => 'individual',
                'accountHolderName' => $bankAccount->account_name,
                'accountNumber' => $bankAccount->account_number,
            ],
            'paymentDestination' => 'bank_account'
        ];
    }

    # create sub accounts
    public function createSubAccount()
    {
        $faker = \Faker\Factory::create();

        $endpoint = "{$this->baseUrl}/profile/business/{$this->getBusinessId()}/sub-accounts";

        $requestBody =  [
            'name' => "{$faker->firstName()} {$faker->lastName()}",
            'email' => "{$faker->email()}",
            'country' => 'NG',
            'mobile' => '09099990099'
        ];

        $request = $this->http()->post($endpoint, $requestBody);

        if ($request->ok() && $request->object()?->success === true) {
            return $request->object()?->data;
        } else {
            throw new RuntimeException($request->object()->error);
        }
    }

    public function fetchSubAccounts()
    {
        $endpoint = "{$this->baseUrl}/profile/business/{$this->getBusinessId()}/sub-accounts";

        $request = $this->http()->get($endpoint);

        if ($request->ok() && $request->object()?->success === true) {
            return $request->object()?->data;
        } else {
            throw new RuntimeException($request->object()->error);
        }
    }

    # VAs

    public function createVirtualAccount(string $subAccountId, array $payload)
    {

        $endpoint = "{$this->baseUrl}/profile/virtual-accounts/business/{$this->getBusinessId()}/sub-accounts/{$subAccountId}/requests/AUTO";

        $request = $this->http()->post($endpoint, $payload);

        if ($request->ok() && $request->object()?->success === true) {
            return $request->object()?->data;
        } else {
            throw new RuntimeException($request->object()->error);
        }
    }

    public function http()
    {
        return Http::withHeaders([
            'api-key' => env('FINCRA_API_KEY')
        ]);
    }
}
