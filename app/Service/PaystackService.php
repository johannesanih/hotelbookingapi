<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class PaystackService
{
    protected string $secretKey;
    protected string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
    }

    /**
     * Initialize a Paystack transaction.
     * Returns the authorization_url to redirect the customer to.
     */
    public function initialize(string $email, int $amountInKobo, string $reference, array $metadata = []): array
    {
        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email'     => $email,
                'amount'    => $amountInKobo,
                'reference' => $reference,
                'metadata'  => $metadata,
                'callback_url' => config('services.paystack.callback_url'),
            ]);

        if (!$response->successful() || !$response->json('status')) {
            throw new \Exception('Paystack initialization failed: ' . $response->json('message'));
        }

        return $response->json('data');
    }

    /**
     * Verify a Paystack transaction by reference.
     */
    public function verify(string $reference): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transaction/verify/{$reference}");

        if (!$response->successful() || !$response->json('status')) {
            throw new \Exception('Paystack verification failed: ' . $response->json('message'));
        }

        return $response->json('data');
    }
}
