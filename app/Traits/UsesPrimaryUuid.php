<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UsesPrimaryUuid
{
    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    public static function booted()
    {
        static::creating(function (self $model) {
            $model->setAttribute($model->getKeyName(), Str::uuid());
        });
    }
}
