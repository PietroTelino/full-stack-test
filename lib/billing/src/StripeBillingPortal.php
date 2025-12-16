<?php

namespace Lib\Billing;

use App\Models\User;

/**
 * Esta classe é responsável por gerar a URL de acesso ao Billing Portal do stripe.
 * Ela foi criada para que o fluxo de Checkout pude-se ser testado unitariamente.
 *
 * Não adicione nenhuma lógica aqui nem tente testar essa classe unitariamente,
 * pois ela é apenas um wrapper para o Stripe, e não queremos chamadas externas nos testes unitários.
 */
class StripeBillingPortal
{
    public function redirect(User $user)
    {
        return $user->redirectToBillingPortal(route('dashboard'));
    }
}
