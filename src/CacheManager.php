<?php

namespace LaravelAutoCache;

use Cache;

class CacheManager
{
    /**
     * Disabled models
     * @var array
     */
    private $disabled = [];

    public function getDisabled(): array
    {
        return $this->disabled;
    }

    public function disable(string $table)
    {
        if (false === array_search($table, $this->disabled)) {
            array_push($this->disabled, $table);
        }

        return $this;
    }

    public function enable(string $table)
    {
        $key = array_search($table, $this->disabled);

        if (false !== $key) {
            unset($this->disabled[$key]);
        }

        return $this;
    }

    public function isDisabled(string $table): bool
    {
        return in_array($table, $this->disabled);
    }

    public function remove(string $table)
    {
        if (method_exists(Cache::getStore(), 'tags')) {
            Cache::tags([config('autocache.prefix') . $table])->flush();
        }
    }

    public function remember(string $table, string $key, $callback)
    {
        if (method_exists(Cache::getStore(), 'tags')) {
            return Cache::tags([config('autocache.prefix') . $table])->rememberForever($key, function () use ($callback) {
                return $callback;
            });
        }

        return $callback;
    }
}
