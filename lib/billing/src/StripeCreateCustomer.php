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
class StripeCreateCustomer
{
    public function create(User $user)
    {
        // Associa o usuário ao Stripe.
        // Com essa informação, a métrica de tempo de conversão
        // de usuário para assinante pode ser calculada.
        $user->createOrGetStripeCustomer();
    }
}
