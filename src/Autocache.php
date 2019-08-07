<?php

namespace LaravelAutoCache;

use LaravelAutoCache\CacheManager;

trait Autocache
{
    /**
     * Get a new query builder instance for the connection.
     *
     * @return \App\Support\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        $builder = in_array(self::class, config('autocache.models'))
            ? \LaravelAutoCache\Builder::class
            : \Illuminate\Database\Query\Builder::class;

        return new $builder($connection, $connection->getQueryGrammar(), $connection->getPostProcessor());
    }

    public static function disableAutoCache()
    {
        app(CacheManager::class)->disable((new self)->getTable());
    }

    public static function enableAutoCache()
    {
        app(CacheManager::class)->enable((new self)->getTable());
    }
}
