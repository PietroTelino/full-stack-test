<?php

namespace Lib\Billing;

use App\Models\User;

/**
 * Esta classe é responsável por gerar a URL de checkout do Stripe.
 * Ela foi criada para que o fluxo de Checkout pude-se ser testado unitariamente.
 *
 * Não adicione nenhuma lógica aqui nem tente testar essa classe unitariamente,
 * pois ela é apenas um wrapper para o Stripe, e não queremos chamadas externas nos testes unitários.
 */
class StripeCheckout
{
    public function redirect(User $user, \Stripe\Price $price)
    {
        /**
         * O nome do produto é definido no Stripe Dashboard.
         * O mapping de nomes internos permite não acoplar o nome do produto no código.
         */
        $name = config('billing.product_name_map.'.$price->product->name);

        $checkout = $user
            ->newSubscription($name, $price->id)
            ->checkout([
                // @todo: Adicionar a Página de sucesso
                'ui_mode' => 'embedded',
                'return_url' => route('billing.checkout.return').'?session_id={CHECKOUT_SESSION_ID}',
                'allow_promotion_codes' => true,
            ]);

        return response()->json([
            'clientSecret' => $checkout->asStripeCheckoutSession()->client_secret,
        ]);
    }
}
