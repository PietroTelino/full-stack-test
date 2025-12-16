<?php

namespace Lib\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use Lib\Billing\StripeBillingPortal;
use Illuminate\Http\Request;

class BillingPortalController extends Controller
{
    public function __construct(private StripeBillingPortal $billingPortal) {}

    public function __invoke(Request $request)
    {
        if ($request->user()->subscriptions()->exists()) {
            /**
             * @see https://inertiajs.com/redirects
             * Necessário para redirects externos.
             */
            return inertia()->location($this->billingPortal->redirect(auth()->user()));
        }

        return redirect()->route('billing.products');
    }
}
