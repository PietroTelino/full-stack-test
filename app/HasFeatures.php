<?php

namespace App;

/**
 * @property array<int, string> $features
 */
trait HasFeatures
{
    public $features = [];

    public static function bootHasFeatures()
    {
        static::retrieved(function ($model) {
            $model->features = Features::boot($model);
        });
    }

    public function hasFeature(string $feature)
    {
        if (is_array($this->features)) {
            return in_array($feature, $this->features);
        }
        return false;
    }

    public function addFeature(string $feature)
    {
        $this->features = array_merge($this->features, [$feature]);
        return $this;
    }

    public function removeFeature(string $feature)
    {
        $this->features = array_diff($this->features, [$feature]);
        return $this;
    }
}
