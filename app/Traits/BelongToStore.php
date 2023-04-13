<?php

namespace App\Traits;

trait BelongToStore
{
    public function store()
    {
        return $this->belongsTo(Store::class);
    }


    // it will be called automatically when booted
    //boot + trait name
    protected static function bootBelongToStore()
    {
        self::addGlobalScope('store', function ($builder) {
            $builder->where('store_id', app('store.active')->id);
        });
    }
}
