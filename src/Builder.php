<?php

namespace LaravelAutoCache;

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
            return app(CacheManager::class)->remember($this->from, $this->getCacheKey(), parent::runSelect());
        }

        return parent::runSelect();
    }

    protected function applies(): bool
    {
        return request()->isMethod('get')
            && !app(CacheManager::class)->isDisabled($this->from);
    }

    /**
     * Returns a Unique String that can identify the current Query.
     *
     * @return string
     */
    protected function getCacheKey()
    {
        // create cache entry 'prefix-table-sha1' with key 'table'
        return config('autocache.prefix') . $this->from . '-' . sha1(config('autocache.key') . $this->toSql() . json_encode($this->getBindings()));
    }
}
