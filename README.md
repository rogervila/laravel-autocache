<p align="center"><img width="128" src="https://image.flaticon.com/icons/svg/83/83880.svg" alt="Laravel Autocache" /></p>

[![Build Status](https://travis-ci.org/rogervila/laravel-autocache.svg?branch=master)](https://travis-ci.org/rogervila/laravel-autocache)
[![Build status](https://ci.appveyor.com/api/projects/status/3naje594j8rpv3x9?svg=true)](https://ci.appveyor.com/project/roger-vila/laravel-autocache)
[![StyleCI](https://github.styleci.io/repos/179132676/shield?branch=master)](https://github.styleci.io/repos/179132676)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=rogervila_laravel-autocache&metric=alert_status)](https://sonarcloud.io/dashboard?id=rogervila_laravel-autocache)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=rogervila_laravel-autocache&metric=coverage)](https://sonarcloud.io/dashboard?id=rogervila_laravel-autocache)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=rogervila_laravel-autocache&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=rogervila_laravel-autocache)
[![Latest Stable Version](https://poser.pugx.org/rogervila/laravel-autocache/v/stable)](https://packagist.org/packages/rogervila/laravel-autocache)
[![Total Downloads](https://poser.pugx.org/rogervila/laravel-autocache/downloads)](https://packagist.org/packages/rogervila/laravel-autocache)
[![License](https://poser.pugx.org/rogervila/laravel-autocache/license)](https://packagist.org/packages/rogervila/laravel-autocache)

# Laravel Autocache

## About

Laravel Autocache package caches Eloquent model 'select' queries.

When a model is modified with [Eloquent methods](https://laravel.com/docs/5.8/eloquent#inserting-and-updating-models), the cache is automatically flushed.

## Example

Imagine that we select all Posts with Categories using [Eager Loading](https://laravel.com/docs/5.8/eloquent-relationships#eager-loading)

```php
Post::with('categories')->get();
```

This will generate two queries that will run only once:

```sh
select * from `posts`
select * from `categories`
```
While no Post or Category model change is applied, the query will be cached.

Now, imagine that we edit the title of our latest post

```php
$post = Post::find($id);
$post->update(['title' => 'Your edited title']);

return Post::with('categories')->get();
```

Only the select query for `posts` table will be executed, since Category model stills cached

```sh
select * from `categories`
```

Autocache can be disabled on runtime if necessary

```php
Post::disableAutoCache();

// Do your database changes here

Post::enableAutoCache();

```

## Installation

Require this package with composer.

```sh
composer require rogervila/laravel-autocache
```

If you don't use auto-discovery, add the ServiceProvider to the providers array in config/app.php

```php
LaravelAutoCache\AutocacheServiceProvider::class,
```

Now, copy the package config to your project config with the publish command:

```sh
php artisan vendor:publish --provider=" LaravelAutoCache\AutocacheServiceProvider"
```


## Usage

First, put the models you want to handle into `config/autocache.php` models key.

```php
/**
 * List of models that implement autocache by default.
 * Models have to also implement the Autocache trait
 * in order to work properly
 */
'models' => [
    App\Product::class,
],
```

Then, add the `Autocache` trait on the models listed on the configuration

```php
namespace App;

use Illuminate\Database\Eloquent\Model;
use LaravelAutoCache\Autocache;

class Product extends Model
{
    use Autocache;
    ...
```

## Troubleshooting

#### Autocache does not work on update queries

Check this [Laravel issue comment](https://github.com/laravel/framework/issues/11777#issuecomment-170384117)

## License

Laravel Autocache is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Icon made by <a href="https://www.flaticon.com/authors/dirtyworks" title="Dirtyworks">Dirtyworks</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a>
