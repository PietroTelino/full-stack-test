<?php

namespace Lib\Tenancy;

use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property $team_id
 */
trait Tenantable
{
    public static function bootTenantable()
    {
        static::creating(function ($model) {
            Tenant::assertQuery();
            if (Tenant::exists()) {
                $model->team_id = Tenant::current()->id();
            }
        });

        static::addGlobalScope('tenantable', function (Builder $builder) {
            Tenant::assertQuery();
            if (Tenant::exists()) {
                // table prefix is necessary when performing joins.
                $builder->where((new self)->getTable().'.team_id', Tenant::current()->id());
            }
        });
    }

    public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null, $relation = null): BelongsToMany
    {
        /** @var BelongsToMany */
        $belongsToMany = parent::belongsToMany($related, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relation);

        Tenant::assertQuery();
        if (Tenant::exists()) {
            $belongsToMany->withPivotValue('team_id', Tenant::current()->id());
        }

        return $belongsToMany;
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function tenantId(): TenantId
    {
        return new TenantId($this->team_id);
    }

    public function scopeAsLandlord($query)
    {
        $query->withoutGlobalScope('tenantable');
    }

    public function scopeAsTenant($query, TenantId $tenantId)
    {
        $query->withoutGlobalScope('tenantable')->where('team_id', $tenantId->id());
    }
}
