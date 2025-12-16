<?php

namespace Lib\Tenancy;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TenantResourceCollection extends AnonymousResourceCollection
{
    public function resolve($request = null)
    {
        $data = parent::resolve($request);
        $this->collection->each(function ($jsonResource, $index) use (&$data) {
            if (in_array(Tenantable::class, class_uses_recursive($jsonResource->resource))) {
                $data[$index]['team_id'] = $jsonResource->resource->team_id;
            }
        });

        return $data;
    }
}
