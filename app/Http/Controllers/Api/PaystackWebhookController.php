<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    /**
     * Handle incoming Paystack webhook events.
     * This route must be excluded from CSRF and auth middleware.
     */
    public function handle(Request $request)
    {
        // Step 1: Verify the request is genuinely from Paystack
        $paystackSignature = $request->header('x-paystack-signature');
        $computedSignature = hash_hmac('sha512', $request->getContent(), config('services.paystack.secret_key'));

        if ($paystackSignature !== $computedSignature) {
            Log::warning('Paystack webhook signature mismatch.');
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $payload = $request->all();
        $event   = $payload['event'] ?? null;
        $data    = $payload['data'] ?? [];

        Log::info('Paystack webhook received.', ['event' => $event]);

        match($event) {
            'charge.success' => $this->handleChargeSuccess($data),
            'charge.failed'  => $this->handleChargeFailed($data),
            default          => Log::info("Unhandled Paystack event: {$event}"),
        };

        // Always return 200 to acknowledge receipt
        return response()->json(['message' => 'Webhook received.'], 200);
    }

    private function handleChargeSuccess(array $data): void
    {
        $payment = Payment::where('reference', $data['reference'])->first();

        if (!$payment) {
            Log::warning('Paystack webhook: payment not found.', ['reference' => $data['reference']]);
            return;
        }

        if ($payment->status === 'completed') {
            return; // Already handled, skip
        }

        $payment->update([
            'status'          => 'completed',
            'gatewayresponse' => json_encode($data),
        ]);

        $payment->booking->update(['status' => 'confirmed']);

        Log::info('Paystack payment completed.', ['reference' => $data['reference']]);
    }

    private function handleChargeFailed(array $data): void
    {
        $payment = Payment::where('reference', $data['reference'])->first();

        if (!$payment) {
            Log::warning('Paystack webhook: payment not found.', ['reference' => $data['reference']]);
            return;
        }

        $payment->update([
            'status'          => 'failed',
            'gatewayresponse' => json_encode($data),
        ]);

        Log::info('Paystack payment failed.', ['reference' => $data['reference']]);
    }
}
