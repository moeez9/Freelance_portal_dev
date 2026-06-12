<?php

namespace App\Http\Controllers;

use App\Models\Gig;
use App\Models\GigOrder;
use App\Models\GigPackage;
use App\Models\Payment;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\UserNotification;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class GigOrderController extends Controller
{
    public function candidateOrders()
    {
        $query = GigOrder::with(['gig', 'package', 'client'])
            ->whereHas('gig', function ($gigQuery) {
                $gigQuery->where('freelancer_id', Auth::id());
            })
            ->latest();

        if (Schema::hasColumn('gig_orders', 'payment_verified_at')) {
            $query->whereNotNull('payment_verified_at');
        } else {
            $query->whereRaw('1 = 0');
        }

        $orders = $query->paginate(12);

        return view('candidates-orders', compact('orders'));
    }

    public function store(Request $request, Gig $gig)
    {
        if (!Auth::check() || Auth::user()->role !== 'employer') {
            return back()->with('error', 'Only employers can order gigs.');
        }

        if ($gig->freelancer_id === Auth::id()) {
            return back()->with('error', 'You cannot order your own gig.');
        }

        $selectedPackageId = $request->input('gig_package_id', $request->input('package_id'));
        $request->merge(['gig_package_id' => $selectedPackageId]);
        $request->validate([
            'gig_package_id' => 'required|integer',
            'payment_method' => 'required|in:credit_card,debit_card,jazzcash,easypaisa',
            'payer_name' => 'required|string|max:120',
            'payer_contact' => 'required|string|max:50',
            'transaction_reference' => 'required|string|max:100',
        ]);

        $package = GigPackage::where('gig_id', $gig->id)->where('id', $request->gig_package_id)->first();
        if (!$package) {
            return back()->with('error', 'Selected package does not belong to this gig.');
        }

        $orderData = [
            'gig_id' => $gig->id,
            'client_id' => Auth::id(),
            'status' => 'pending',
        ];
        if (Schema::hasColumn('gig_orders', 'payment_method')) {
            $orderData['payment_method'] = $request->payment_method;
        }
        if (Schema::hasColumn('gig_orders', 'payer_name')) {
            $orderData['payer_name'] = $request->payer_name;
        }
        if (Schema::hasColumn('gig_orders', 'payer_contact')) {
            $orderData['payer_contact'] = $request->payer_contact;
        }
        if (Schema::hasColumn('gig_orders', 'transaction_reference')) {
            $orderData['transaction_reference'] = $request->transaction_reference;
        }
        if (Schema::hasColumn('gig_orders', 'gig_package_id')) {
            $orderData['gig_package_id'] = $package->id;
        }
        if (Schema::hasColumn('gig_orders', 'package_id')) {
            $orderData['package_id'] = $package->id;
        }

        $order = GigOrder::create($orderData);

        Payment::updateOrCreate(
            [
                'type' => 'gig',
                'reference_id' => $order->id,
            ],
            [
                'job_id' => null,
                'user_id' => Auth::id(),
                'amount' => (float) $package->price,
                'status' => 'pending',
            ]
        );

        return back()
            ->with('success', 'Order placed. Payment is pending admin verification.');
    }

    public function verifyDummyPayment(GigOrder $order)
    {
        $user = Auth::user();
        $adminEmail = strtolower((string) config('payments.manual_admin.email'));
        $canVerify = strtolower((string) $user->email) === $adminEmail || $user->id === $order->client_id;
        if (!$canVerify) {
            abort(403);
        }

        if ($order->payment_verified_at) {
            return back()->with('success', 'Payment is already verified.');
        }

        $verificationData = [];
        if (Schema::hasColumn('gig_orders', 'payment_verified_at')) {
            $verificationData['payment_verified_at'] = Carbon::now();
        }
        if (Schema::hasColumn('gig_orders', 'payment_verified_by')) {
            $verificationData['payment_verified_by'] = $user->id;
        }
        if (!empty($verificationData)) {
            $order->update($verificationData);
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

        try {
            $freelancer = $order->gig->freelancer;
            if ($freelancer && $freelancer->email) {
                Mail::raw(
                    "Payment verified (demo): Employer {$order->client?->name} selected package {$order->package?->name} for gig {$order->gig?->title}. Please start work as per package details.",
                    function ($message) use ($freelancer) {
                        $message->to($freelancer->email, $freelancer->name)->subject('Gig Payment Verified - Start Work');
                    }
                );
            }
        } catch (Throwable) {
            // Dummy flow for presentation; ignore mail transport issues.
        }

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
            // Keep demo flow resilient if conversation setup fails.
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

        return back()->with('success', 'Dummy payment verified and freelancer notified.');
    }

    public function updateStatus(Request $request, GigOrder $order)
    {
        $user = Auth::user();

        if ($user->id === $order->gig->freelancer_id) {
            $request->validate([
                'status' => 'required|in:accepted,delivered',
                'clarification_message' => 'nullable|string|max:1000',
            ]);

            if ($request->status === 'accepted' && $order->status !== 'pending') {
                return back()->with('error', 'Order can only be accepted when pending.');
            }
            if ($request->status === 'delivered' && !in_array($order->status, ['accepted', 'revision_requested'], true)) {
                return back()->with('error', 'Order can only be delivered after acceptance or revision request.');
            }

            $order->update([
                'status' => $request->status,
                'clarification_message' => $request->clarification_message,
            ]);
        } elseif ($user->id === $order->client_id) {
            $request->validate([
                'status' => 'required|in:completed,revision_requested',
                'clarification_message' => 'nullable|string|max:1000',
            ]);

            if (!in_array($order->status, ['delivered', 'revision_requested'], true) && $request->status === 'completed') {
                return back()->with('error', 'Order can only be completed after delivery.');
            }
            if ($request->status === 'revision_requested' && $order->status !== 'delivered') {
                return back()->with('error', 'Revision can only be requested after delivery.');
            }

            $order->update([
                'status' => $request->status,
                'clarification_message' => $request->clarification_message,
            ]);
        } else {
            abort(403);
        }

        return back()->with('success', 'Order status updated.');
    }
}
