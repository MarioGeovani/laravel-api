<?php

namespace Tests\Unit;

use App\Cache\CacheManager;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CacheManagerTest extends TestCase
{
    use WithFaker;
    private CacheManager $cacheManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->cacheManager = new CacheManager();
    }

    public function test_set_and_get(): void
    {
        $key = $this->faker()->uuid();
        $value = [$this->faker()->text()];
        $cacheType = CacheManager::LOCATION_LIST_KEY;
        $this->cacheManager->set($key, $cacheType, $value);

        $cachedValue = $this->cacheManager->get($key, $cacheType);
        $this->assertEquals($value, $cachedValue);
    }

}
