<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Service\PaystackService;
use Illuminate\Http\Request;

class CustomerPaymentController extends Controller
{
    public function __construct(protected PaystackService $paystackService) {}

    /**
     * Initialize a Paystack payment for a booking.
     * Returns the Paystack authorization URL to redirect the customer to.
     */
    public function initialize(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::with('payment')->findOrFail($request->booking_id);

        $payment = $booking->payment;

        if (!$payment) {
            return response()->json([
                'message' => 'No payment record found for this booking.',
            ], 404);
        }

        $this->authorize('initialize', $payment);

        if ($payment->status === 'completed') {
            return response()->json([
                'message' => 'This booking has already been paid for.',
            ], 422);
        }

        if ($payment->status === 'cancelled') {
            return response()->json([
                'message' => 'This payment has been cancelled.',
            ], 422);
        }

        try {
            // Paystack expects amount in kobo (multiply by 100)
            $amountInKobo = (int) ($payment->amount * 100);

            $data = $this->paystackService->initialize(
                email: $request->user()->email,
                amountInKobo: $amountInKobo,
                reference: $payment->reference,
                metadata: [
                    'booking_id' => $booking->id,
                    'user_id'    => $request->user()->id,
                    'hotel_id'   => $booking->hotel_id,
                ],
            );

            return response()->json([
                'message'           => 'Payment initialized.',
                'authorization_url' => $data['authorization_url'],
                'reference'         => $payment->reference,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify a Paystack payment after the customer is redirected back.
     * Paystack appends ?reference=xxx to the callback URL.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $payment = Payment::where('reference', $request->reference)->firstOrFail();

        $this->authorize('view', $payment);

        if ($payment->status === 'completed') {
            return response()->json([
                'message' => 'Payment already verified.',
                'payment' => $payment,
            ], 200);
        }

        try {
            $data = $this->paystackService->verify($request->reference);

            $status = match($data['status']) {
                'success' => 'completed',
                'failed'  => 'failed',
                default   => 'pending',
            };

            $payment->update([
                'status'          => $status,
                'gatewayresponse' => json_encode($data),
            ]);

            // Confirm the booking if payment succeeded
            if ($status === 'completed') {
                $payment->booking->update(['status' => 'confirmed']);
            }

            return response()->json([
                'message' => $status === 'completed' ? 'Payment successful.' : 'Payment not completed.',
                'payment' => $payment->fresh(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Public callback endpoint — Paystack redirects here after payment.
     * No auth required since this is a browser redirect with no token.
     * Auto-verifies using the reference in the query string.
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        if (!$reference) {
            return response()->json([
                'message' => 'No payment reference found in callback.',
            ], 422);
        }

        $payment = Payment::where('reference', $reference)->firstOrFail();

        if ($payment->status === 'completed') {
            return response()->json([
                'message' => 'Payment already verified.',
                'payment' => $payment->load('booking'),
            ], 200);
        }

        try {
            $data = $this->paystackService->verify($reference);

            $status = match($data['status']) {
                'success' => 'completed',
                'failed'  => 'failed',
                default   => 'pending',
            };

            $payment->update([
                'status'          => $status,
                'gatewayresponse' => json_encode($data),
            ]);

            if ($status === 'completed') {
                $payment->booking->update(['status' => 'confirmed']);
            }

            return response()->json([
                'message' => $status === 'completed' ? 'Payment successful.' : 'Payment not completed.',
                'payment' => $payment->fresh('booking'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
