<?php

namespace Lib\Tenancy;

use App\Models\Team;
use Closure;

class Tenant
{
    private static ?TenantId $tenantId = null;

    private static bool $landlord = false;

    private static bool $runningUnitTests = false;

    private static string $timezone = 'UTC';

    public static function exists(): bool
    {
        return ! is_null(static::current());
    }

    public static function isLandlord(): bool
    {
        return static::$landlord === true;
    }

    public static function runningUnitTests(bool $value): void
    {
        static::$runningUnitTests = $value;
    }

    public static function assertQuery()
    {
        if (! static::exists() && ! static::$runningUnitTests) {
            throw_unless(static::isLandlord(), 'Multi-tenancy is enabled but no tenant is set.');
        }
    }

    /**
     * Landlord é o contexto global, onde todos os tenants estão disponíveis.
     * Deve ser utilizado em casos onde é necessário acessar dados de todos os tenants.
     * I.e.: seeders, commands, usuário sysadmin, etc.
     */
    public static function asLandlord(?Closure $callback = null)
    {
        $originalTenant = static::$tenantId;
        static::forget();
        static::$landlord = true;
        if ($callback) {
            $result = rescue(fn () => $callback(), null, false);
            if ($originalTenant) {
                static::makeCurrent($originalTenant);
            }
            static::$landlord = false;

            return $result;
        }
    }

    public static function asTenant(TenantId $tenantId, string $timezone, Closure $callback)
    {
        // --------------------------------------------------------------
        // O originalTenant é utilizado para restaurar o contexto após
        // a execução do callback. Esse valor é nulo quando o contexto
        // é um script da aplicação, i.e. seeders, commands, etc.
        // --------------------------------------------------------------
        $originalTenant = static::$tenantId;
        $originalTimezone = static::$timezone;
        static::makeCurrent($tenantId, $timezone);
        $result = rescue(fn () => $callback(), null, false);

        // --------------------------------------------------------------
        // Caso haja um tenant original, restaura o contexto para ele.
        // Se não, remove o tenant assumido.
        // --------------------------------------------------------------
        if ($originalTenant) {
            static::makeCurrent($originalTenant, $originalTimezone);
        } else {
            static::forget();
        }

        return $result;
    }

    public static function current(): ?TenantId
    {
        return static::$tenantId;
    }

    public static function makeCurrent(TenantId $tenantId, string $timezone = 'UTC')
    {
        static::$tenantId = $tenantId;
        static::setTimezone($timezone);
    }

    /**
     * Essa função está pública apenas para ser utilizada em testes unitários!
     */
    public static function setTimezone(string $timezone)
    {
        static::$timezone = $timezone;
    }

    public static function getTimezone()
    {
        return static::$timezone;
    }

    public static function forget()
    {
        static::$tenantId = null;
        static::$timezone = 'UTC';
    }

    /**
     * Reseta o contexto para o estado inicial.
     */
    public static function reset()
    {
        static::$landlord = false;
        static::forget();
        static::$runningUnitTests = false;
    }

    public static function owner()
    {
        if (Tenant::current()) {
            return cache()->remember(sprintf('team::%s::owner', Tenant::current()->id()), now()->addMinutes(1), function () {
                return Team::with('owner')->find(Tenant::current()->id())?->owner;
            });
        }
    }

    public static function team()
    {
        if (Tenant::current()) {
            return cache()->remember(sprintf('team::%s', Tenant::current()->id()), now()->addMinutes(1), function () {
                return Team::find(Tenant::current()->id());
            });
        }
    }
}
