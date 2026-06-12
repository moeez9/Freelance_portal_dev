<?php

namespace App\Http\Controllers;

use App\Models\GigOrder;
use App\Models\Payment;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AdminDemoPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = GigOrder::with(['gig.freelancer', 'package', 'client'])->latest();

        if (Schema::hasColumn('gig_orders', 'payment_verified_at')) {
            $status = $request->query('status');
            if ($status === 'pending') {
                $query->whereNull('payment_verified_at');
            } elseif ($status === 'verified') {
                $query->whereNotNull('payment_verified_at');
            }
        }

        $orders = $query->paginate(12)->withQueryString();

        return view('admin-demo-payments', [
            'orders' => $orders,
            'adminEmail' => (string) $request->session()->get('admin_auth.email'),
        ]);
    }

    public function verify(GigOrder $order)
    {
        if ($order->payment_verified_at) {
            return back()->with('success', 'Payment already verified.');
        }

        $data = [];
        if (Schema::hasColumn('gig_orders', 'payment_verified_at')) {
            $data['payment_verified_at'] = Carbon::now();
        }
        if (Schema::hasColumn('gig_orders', 'payment_verified_by')) {
            $data['payment_verified_by'] = null;
        }
        if (!empty($data)) {
            $order->update($data);
        }

        Payment::updateOrCreate(
            [
                'type' => 'gig',
                'reference_id' => $order->id,
            ],
            [
                'job_id' => null,
                'user_id' => $order->client_id,
                'amount' => (float) ($order->package?->price ?? 0),
                'status' => 'released',
            ]
        );

        $messageUrl = route('messages.index');
        try {
            $conversation = Conversation::firstOrCreate(
                [
                    'context_type' => 'gig_order',
                    'context_id' => $order->id,
                ],
                [
                    'created_by' => (int) $order->client_id,
                ]
            );

            ConversationParticipant::firstOrCreate([
                'conversation_id' => $conversation->id,
                'user_id' => (int) $order->client_id,
            ]);
            ConversationParticipant::firstOrCreate([
                'conversation_id' => $conversation->id,
                'user_id' => (int) ($order->gig?->freelancer_id),
            ]);

            $messageUrl = route('messages.show', $conversation);
        } catch (Throwable) {
            // Ignore conversation setup errors in demo flow.
        }

        $freelancerId = $order->gig?->freelancer_id;
        if ($freelancerId) {
            UserNotification::create([
                'user_id' => $freelancerId,
                'type' => 'gig_payment_verified',
                'title' => 'Payment verified for gig order',
                'message' => ($order->client?->name ?? 'Employer') . ' selected package "' . ($order->package?->name ?? 'N/A') . '" for "' . ($order->gig?->title ?? 'Gig') . '". Click to view details and contact buyer.',
                'data' => [
                    'gig_id' => $order->gig_id,
                    'gig_order_id' => $order->id,
                    'package_name' => $order->package?->name,
                    'buyer_name' => $order->client?->name,
                    'target_url' => $messageUrl,
                ],
            ]);
        }

        try {
            $freelancer = $order->gig->freelancer;
            if ($freelancer && $freelancer->email) {
                Mail::raw(
                    "Payment verified (admin panel): Employer {$order->client?->name} selected package {$order->package?->name} for gig {$order->gig?->title}.",
                    function ($message) use ($freelancer) {
                        $message->to($freelancer->email, $freelancer->name)->subject('Gig Payment Verified - Admin');
                    }
                );
            }
        } catch (Throwable) {
            // Ignore mail issues in demo flow.
        }

        return back()->with('success', 'Payment verified from admin panel.');
    }
}
