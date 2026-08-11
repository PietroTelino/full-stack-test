<?php

namespace Lib\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

class CheckoutReturnController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string', 'max:255', 'regex:/^cs_[A-Za-z0-9_]+$/'],
        ]);

        try {
            $session = Cashier::stripe()->checkout->sessions->retrieve($validated['session_id']);
        } catch (\Stripe\Exception\InvalidRequestException) {
            abort(404);
        }

        return inertia('CheckoutReturn', [
            'status' => $session->status,
        ]);
    }
}
