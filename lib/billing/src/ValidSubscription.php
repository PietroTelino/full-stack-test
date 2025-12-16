<?php

namespace Lib\Billing;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Subscription;

trait ValidSubscription
{
    public function getSubscriptions()
    {
        $this->loadMissing('subscriptions');

        return $this->subscriptions;
    }

    public function neverSubscribed(): bool
    {
        return $this->getSubscriptions()->isEmpty();
    }

    /**
     * Deixando de retornar um HasMany para evitar duplicação de queries desnecessárias, principalmente no módulo de bot
     */
    public function validSubscriptions()
    {
        // O método onTrial do Cashier faz a verificação de trial estrita, i.e. trial_ends_at tem que estar preenchido.
        // Porém, uma assinatura ativa não vai ter o trial_ends_at preenchido e portanto, pelo cashier, não seria considerada válida
        // no chain de métodos abaixo. Por isso, a verificação de trial_ends_at é feita manualmente.
        return $this->getSubscriptions()
            ->filter->valid()
            ->filter(function (Subscription $subscription) {
                // Verifica se a assinatura é válida antes de verificar o trial_ends_at
                // pois já ocorreu cenários de usuário com assinatura válida ter o trial_ends_at preenchido e no passado
                // e mesmo com a assinatura válida era considerado inválida.
                if ($subscription->valid()) {
                    return true;
                }

                return $subscription->trial_ends_at === null || $subscription->trial_ends_at->isFuture();
            })->flatten();
    }

    public function hasAnySubscription(): bool
    {
        return $this->getSubscriptions()->isNotEmpty();
    }

    public function hasAnyValidSubscription(): bool
    {
        $hasAnyValidSubscription = $this->validSubscriptions()->isNotEmpty();

        return $hasAnyValidSubscription;
    }

    public function isAccessBlocked(): bool
    {
        if ($this->hasAnyValidSubscription()) {
            return false;
        }

        /**
         * Essa chamada faz queries duplicadas desnecessáriamente no cenário que usamos.
         * Porém para não duplica a lógica de verificação de trial não foi removida.
         */
        return ! $this->onTrial();
    }
}
