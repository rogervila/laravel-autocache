<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use LaravelAutoCache\CacheManager;
use Tests\TestModel;

class CacheTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * @param $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'AlternativeLaravelCache\Provider\AlternativeCacheStoresServiceProvider',
            'LaravelAutoCache\AutocacheServiceProvider',
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('autocache.models', [
            TestModel::class,
        ]);
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $this->loadMigrationsFrom(__DIR__ . '/../database');

        $this->dummy = uniqid();

        $this->model = new TestModel;
    }

    /**
     * @return void
     */
    public function testDummyIsInserted()
    {
        Cache::flush();
        TestModel::create(['dummy' => $this->dummy]);

        $this->assertDatabaseHas($this->model->getTable(), [
            'dummy' => $this->dummy,
        ]);
    }

    /**
     * @return void
     */
    public function testCacheWorksWithTags()
    {
        Cache::flush();
        Cache::tags(['tag1', 'tag2'])->put('tag-test1', 'ok', 20);

        $this->assertEquals(Cache::tags(['tag2'])->get('tag-test1'), 'ok');
    }

    /**
     * @return void
     */
    public function testModelIsCached()
    {
        $key = 'autocache-test_models-13e52dbd003f48a9279d6f5c71f6d38052ab5c9d';

        Cache::flush();

        $this->assertFalse(
            Cache::tags([config('autocache.prefix') . $this->model->getTable()])->has($key)
        );

        TestModel::select(['dummy'])->get();

        $this->assertTrue(
            Cache::tags([config('autocache.prefix') . $this->model->getTable()])->has($key)
        );
    }

    /**
     * @return void
     */
    public function testModelCacheCanBeDisabled()
    {
        $key = 'autocache-test_models-13e52dbd003f48a9279d6f5c71f6d38052ab5c9d';

        Cache::flush();

        TestModel::disableAutoCache();

        $this->assertContains('test_models', app(CacheManager::class)->getDisabled());

        TestModel::select(['dummy'])->get();

        $this->assertFalse(
            Cache::tags([config('autocache.prefix') . $this->model->getTable()])->has($key)
        );

        TestModel::enableAutoCache();

        TestModel::select(['dummy'])->get();

        $this->assertTrue(
            Cache::tags([config('autocache.prefix') . $this->model->getTable()])->has($key)
        );
    }

    public function testConfigFileKeyIsHashed()
    {
        $key = env('APP_KEY');

        $config = require __DIR__ . '/../../config/autocache.php';

        $this->assertEquals(
            sha1($key),
            $config['key']
        );
    }

    /**
     * Check this link for more information: https://github.com/laravel/framework/issues/11777#issuecomment-170384117
     *
     * @return void
     */
    public function testQueryCacheIsDeletedAfterDatabaseChange()
    {
        $key = 'autocache-test_models-13e52dbd003f48a9279d6f5c71f6d38052ab5c9d';

        Cache::flush();

        TestModel::select(['dummy'])->get();

        $this->assertTrue(
            Cache::tags([config('autocache.prefix') . $this->model->getTable()])->has($key)
        );

        // Create
        TestModel::create(['othercolumn' => 'foo']);

        $this->assertFalse(
            Cache::tags([config('autocache.prefix') . $this->model->getTable()])->has($key)
        );

        TestModel::select(['dummy'])->get();

        $this->assertTrue(
            Cache::tags([config('autocache.prefix') . $this->model->getTable()])->has($key)
        );

        // Update
        $model = TestModel::where('othercolumn', 'foo')->first();
        $model->update(['othercolumn' => 'bar']);

        $this->assertFalse(
            Cache::tags([config('autocache.prefix') . $this->model->getTable()])->has($key)
        );

        TestModel::select(['dummy'])->get();

        $this->assertTrue(
            Cache::tags([config('autocache.prefix') . $this->model->getTable()])->has($key)
        );


        // Delete
        $model = TestModel::where('othercolumn', 'bar')->first();
        $model->delete();

        $this->assertFalse(
            Cache::tags([config('autocache.prefix') . $this->model->getTable()])->has($key)
        );

        TestModel::select(['dummy'])->get();

        $this->assertTrue(
            Cache::tags([config('autocache.prefix') . $this->model->getTable()])->has($key)
        );
    }
}
