<?php

namespace Lib\Tenancy\Events;

use Illuminate\Foundation\Auth\User;
use Lib\Tenancy\TenantId;

class MakingTenantCurrent
{
    /**
     * @property User $user
     * @property int $teamId Team id to use as tenant
     * @property string $timezone User timezone
     */
    public function __construct(
        public readonly User $user,
        public readonly int $teamId,
        public readonly string $timezone
    ) {}

    public static function dispatch(User $user, int $teamId, string $timezone)
    {
        event(new self($user, $teamId, $timezone));
    }

    public function tenantId(): TenantId
    {
        return new TenantId($this->teamId);
    }
}
