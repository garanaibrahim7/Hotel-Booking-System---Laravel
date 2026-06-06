<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\SubscriptionPlans;
use App\Models\Subscriptions;
use App\Services\Subscription\StripeSubscriptionProvider;
use App\Services\SubscriptionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionObj;

    public function __construct()
    {
        $this->subscriptionObj = new SubscriptionService(new StripeSubscriptionProvider);
    }

    public function index()
    {
        $plans = SubscriptionPlans::paginate(10);
        // return $plans;

        return view('admin.subscription.list', compact('plans'));
    }

    public function create()
    {
        $currencies = Country::select(['currency_code', 'currency_symbol'])->get()->pluck('currency_symbol', 'currency_code');

        return view('admin.subscription.add', compact('currencies'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:10',
            'type' => 'required|in:monthly,3 months,6 months,yearly,lifetime',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|min:3',
        ]);

        try {
            $stripeDetails = $this->subscriptionObj->createSubscriptionPlan($validatedData);

            SubscriptionPlans::create([
                'name' => $validatedData['name'],
                'price' => $validatedData['price'],
                'currency' => $validatedData['currency'],
                'currency_symbol' => $validatedData['currency_symbol'],
                'stripe_price_id' => $stripeDetails['stripe_price_id'],
                'stripe_product_id' => $stripeDetails['stripe_product_id'],
                'type' => $validatedData['type'],
                'facilities' => $validatedData['facilities'],
            ]);

            return redirect()->route('admin.subscription.index')
                ->with('success', 'Subscription plan generated and synchronized with Stripe cleanly!');

        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Plan Creating Error: '.$e->getMessage());
        }
    }

    public function edit(SubscriptionPlans $subscription)
    {
        // return $subscription;
        $currencies = Country::select(['currency_code', 'currency_symbol'])->get()->pluck('currency_symbol', 'currency_code');

        return view('admin.subscription.add', [
            'plan' => $subscription,
            'currencies' => $currencies,
        ]);
    }

    public function update(Request $request, SubscriptionPlans $subscription)
    {
        // return $request;
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:10',
            'type' => 'required|in:monthly,3 months,6 months,yearly,lifetime',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|min:3',
        ]);

        try {
            $stripeDetails = $this->subscriptionObj->updateSubscriptionPlan($subscription->stripe_product_id, $validatedData);

            $subscription->update([
                'name' => $validatedData['name'],
                'price' => $validatedData['price'],
                'currency' => $validatedData['currency'],
                'currency_symbol' => $validatedData['currency_symbol'],
                'stripe_price_id' => $stripeDetails['stripe_price_id'],
                'stripe_product_id' => $stripeDetails['stripe_product_id'],
                'type' => $validatedData['type'],
                'facilities' => $validatedData['facilities'] ?? [],
            ]);

            return redirect()->route('admin.subscription.index')
                ->with('success', 'Subscription configurations updated and synchronized seamlessly!');

        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Plan Modifying Error: '.$e->getMessage());
        }
    }

    public function destroy(SubscriptionPlans $subscription)
    {
        $res = $this->subscriptionObj->deleteSubscriptionPlan($subscription->stripe_product_id);
        if ($res['success']) {
            Subscriptions::where('subscription_plan_id', $subscription->id)
                ->delete();
            $subscription->delete();

            return redirect()->back()->with('success', 'Plan Deleted Successfully');
        } else {
            Log::error('Stripe Plan delete Error: '.$res['exception']->getMessage());

            return redirect()->back()->with('error', $res['message']);
        }
    }
}
