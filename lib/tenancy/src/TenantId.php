<?php

namespace Lib\Tenancy;

use JsonSerializable;

final class TenantId implements JsonSerializable
{
    /**
     * @param  string  $id  Team id
     */
    public function __construct(private string $id) {}

    public function id()
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function jsonSerialize(): string
    {
        return $this->id;
    }
}
