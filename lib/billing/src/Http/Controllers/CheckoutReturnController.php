<?php

namespace Lib\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Cashier\Cashier;

class CheckoutReturnController extends Controller
{
    public function __invoke()
    {
        abort_unless(request()->has('session_id'), 404);
        $session = Cashier::stripe()->checkout->sessions->retrieve(request('session_id'));

        return inertia('CheckoutReturn', [
            'status' => $session->status,
        ]);
    }
}
