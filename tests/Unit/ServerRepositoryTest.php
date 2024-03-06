<?php

namespace Tests\Unit;

use App\Cache\CacheManager;
use App\Repositories\ServerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Mockery\MockInterface;
use Tests\TestCase;
use Mockery;

class ServerRepositoryTest extends TestCase
{

    private ServerRepository $serverRepository;
    private MockInterface|CacheManager $cacheManager;

    private string $testUploadDir = 'test-upload';
    private string $testUploadFile = 'the_test_upload_file.xlsx';

    public function setUp(): void
    {
        parent::setUp();

        //define test data
        Config::set('import.upload_folder', $this->testUploadDir);
        Config::set('import.imported_file_name', $this->testUploadFile);
    }

    public function test_that_processServersData_returns_object_no_cache(): void
    {
        $this->cacheManager = Mockery::mock(CacheManager::class);
        $this->serverRepository = new ServerRepository($this->cacheManager);
        $object = $this->serverRepository->processServersData(false);

        $this->assertIsObject($object);
        $this->assertInstanceOf(ServerRepository::class, $object);
    }

    public function test_that_processServersData_returns_object_with_cache(): void
    {
        $this->serverRepository = new ServerRepository(new CacheManager);
        $object = $this->serverRepository->processServersData(true);
        $this->assertIsObject($object);
        $this->assertInstanceOf(ServerRepository::class, $object);
    }

    public function test_that_processServersData_filter_returns_array_no_cache(): void
    {
        $this->cacheManager = Mockery::mock(CacheManager::class);
        $this->serverRepository = new ServerRepository($this->cacheManager);
        $object = $this->serverRepository->processServersData(false);

        $request =  Mockery::mock(Request::class)->shouldIgnoreMissing();
        $request->expects()->validated()->andReturns([]);

        $result = $object->filter($request);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertEquals(8 , $result['count']);
    }

    public function test_that_processServersData_filter_returns_array_with_cache(): void
    {
        $this->serverRepository = new ServerRepository(new CacheManager);
        $object = $this->serverRepository->processServersData(true);

        $request =  Mockery::mock(Request::class)->shouldIgnoreMissing();
        $request->expects()->validated()->andReturns([]);

        $result = $object->filter($request);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertEquals(8 , $result['count']);
    }

    public function test_that_getLocationData_returns_array_no_cache(): void
    {
        $this->cacheManager = Mockery::mock(CacheManager::class);
        $this->serverRepository = new ServerRepository($this->cacheManager);
        $result = $this->serverRepository->getLocationData(false);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function test_that_getLocationData_returns_array_with_cache(): void
    {
        $this->serverRepository = new ServerRepository(new CacheManager);
        $result = $this->serverRepository->getLocationData(true);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

}
