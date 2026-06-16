<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Services\Payments\StripeProvider;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;

class RefundController extends Controller
{
    public function index()
    {
        $refunds = Refund::orderBy('created_at', 'desc')->get()->groupBy('status');

        return view('admin.refund.list', compact('refunds'));
    }

    public function processRefund(Refund $refund)
    {
        $paymentObj = new PaymentService(new StripeProvider);
        $response = $paymentObj->refund($refund->payment_id, $refund->amount, $refund->reason);
        Log::channel('debug')->error('Refund ID : ', ['response' => $response, 'refund' => $refund]);
        $refund_id = $response['data']['refund_id'] ?? null;

        $refund->update([
            'status' => Refund::STATUS_PROCESSING,
            'refund_id' => $refund_id,
        ]);

        if ($response && isset($response['success'])) {
            return back()->with('success', $response['message']);
        }

        return back()->with('error', $response['message'] ?? 'Something went wrong with Refund Process');
    }

    public function rejectRefund(Refund $refund)
    {
        $paymentObj = new PaymentService(new StripeProvider);
        $refund->update([
            'status' => Refund::STATUS_REJECTED,
        ]);

        return back()->with('success', 'Refund Rejected');
    }

    public function show(Refund $refund)
    {
        return redirect()->route('admin.bookings.show', $refund->booking_id);
    }
}
