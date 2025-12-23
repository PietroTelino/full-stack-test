<?php

namespace Lib\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Lib\Billing\StripeBillingPortal;
use Lib\Billing\StripeCheckout;
use Laravel\Cashier\Cashier;

class CheckoutController extends Controller
{
    public function __construct(
        private StripeCheckout $checkout,
        private StripeBillingPortal $billingPortal
    ) {}

    /**
     * Essa implementação leva em consideração a possibilidade de upgrade para o preço anual,
     * esse cenário se torna fácil pois o `lookup_key` do plano anual é diferente do `lookup_key` do plano mensal.
     */
    public function __invoke(string $lookup_key)
    {
        $validated = Validator::make([
            'lookup_key' => $lookup_key,
        ], [
            'lookup_key' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_\-]+$/'],
        ])->validate();

        /**
         * Não utilizamos o middleware de auth pq o redirect feito lá é para a tela de login.
         * Aqui, queremos levar o usuário para tela de cadastro.
         */
        if (! auth()->check()) {
            return redirect()->route('register');
        }

        /** @var User */
        $user = request()->user();

        try {
            $price = Cashier::stripe()->prices->retrieve($validated['lookup_key'], ['expand' => ['product']]);
        } catch (\Stripe\Exception\InvalidRequestException) {
            return response()->noContent(404);
        }

        if ($price) {
            /**
             * Usuário com assinatura incompleta não deve conseguir fazer uma nova assinatura.
             * Ele precisa fazer o pagamento da assinatura incompleta.
             */
            if ($user->subscriptions()->incomplete()->where('stripe_price', $price->id)->exists()) {
                return $this->billingPortal->redirect($user);
            }

            /**
             * Usuário com assinatura ativa no mesmo plano, também não deve conseguir fazer uma nova assinatura.
             * Se for um plano diferente, ele deve conseguir fazer o upgrade.
             */
            if ($user->subscriptions()->active()->where('stripe_price', $price->id)->where('stripe_status', '<>', \Stripe\Subscription::STATUS_TRIALING)->exists()) {
                return $this->billingPortal->redirect($user);
            }

            return $this->checkout->redirect($user, $price);
        }

        return response()->noContent(404);
    }
}
