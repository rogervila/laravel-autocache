<?php

namespace LaravelAutoCache;

use Cache;
use Illuminate\Database\Query\Builder as QueryBuilder;
use LaravelAutoCache\CacheManager;

class Builder extends QueryBuilder
{
    /**
     * Run the query as a "select" statement against the connection.
     *
     * @return array
     */
    protected function runSelect()
    {
        if ($this->applies()) {
            return Cache::tags([config('autocache.prefix') . $this->from])->rememberForever($this->getCacheKey(), function () {
                return parent::runSelect();
            });
        }

        return parent::runSelect();
    }

    /**
     * Checks if the select query can be cached.
     *
     * @return bool
     */
    protected function applies(): bool
    {
        return app(CacheManager::class)->supportsTags()
            && !app(CacheManager::class)->isDisabled($this->from);
    }

    /**
     * Returns a Unique String that can identify the current Query.
     *
     * @return string
     */
    protected function getCacheKey(): string
    {
        // create cache entry 'prefix-table-sha1' with key 'table'
        return config('autocache.prefix') . $this->from . '-' . sha1(config('autocache.key') . $this->toSql() . json_encode($this->getBindings()));
    }
}
