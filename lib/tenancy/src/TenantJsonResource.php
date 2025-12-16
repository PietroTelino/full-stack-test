<?php

namespace Lib\Tenancy;

use Illuminate\Http\Resources\Json\JsonResource;

class TenantJsonResource extends JsonResource
{
    protected static function newCollection($resource)
    {
        return new TenantResourceCollection($resource, static::class);
    }

    public function resolve($request = null)
    {
        $data = parent::resolve($request);
        if (in_array(Tenantable::class, class_uses_recursive($this->resource))) {
            $data['team_id'] = $this->team_id;
        }

        return $data;
    }
}
