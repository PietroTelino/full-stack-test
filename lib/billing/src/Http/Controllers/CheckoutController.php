<?php

namespace Lib\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
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
     * esse cenário se torna fácil pois o `priceId` do plano anual é diferente do `priceId` do plano mensal.
     *
     * @param  $priceId  mixed - Stripe Price ID
     */
    public function __invoke($priceId)
    {
        /**
         * Não utilizamos o middleware de auth pq o redirect feito lá é para a tela de login.
         * Aqui, queremos levar o usuário para tela de cadastro.
         */
        if (! auth()->check()) {
            return redirect()->route('register');
        }

        /** @var User */
        $user = request()->user();

        if ($price = Cashier::stripe()->prices->retrieve($priceId, ['expand' => ['product']])) {
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
