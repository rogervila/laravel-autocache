<?php

namespace LaravelAutoCache;

use Cache;
use Illuminate\Database\Eloquent\Model;
use LaravelAutoCache\CacheManager;

class Observer
{
    /**
     * @param  \App\Model  $model
     * @return void
     */
    public function created(Model $model)
    {
        app(CacheManager::class)->remove($model->getTable());
    }

    /**
     * @param  \App\Model  $model
     * @return void
     */
    public function updated(Model $model)
    {
        app(CacheManager::class)->remove($model->getTable());
    }

    /**
     * @param  \App\Model  $model
     * @return void
     */
    public function saved(Model $model)
    {
        app(CacheManager::class)->remove($model->getTable());
    }

    /**
     * @param  \App\Model  $model
     * @return void
     */
    public function deleted(Model $model)
    {
        app(CacheManager::class)->remove($model->getTable());
    }

    /**
     * @param  \App\Model  $model
     * @return void
     */
    public function restored(Model $model)
    {
        app(CacheManager::class)->remove($model->getTable());
    }
}
