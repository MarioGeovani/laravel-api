<?php

namespace Tests\Unit;

use App\Cache\CacheManager;
use App\Classes\Filter;
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
    private MockInterface|Filter $filter;

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
        $this->filter = Mockery::mock(filter::class);
        $this->serverRepository = new ServerRepository($this->cacheManager,  $this->filter);
        $object = $this->serverRepository->processServersData(false);

        $this->assertIsObject($object);
        $this->assertInstanceOf(ServerRepository::class, $object);
    }

    public function test_that_processServersData_returns_object_with_cache(): void
    {
        $this->filter = Mockery::mock(filter::class);
        $this->serverRepository = new ServerRepository(new CacheManager, $this->filter);
        $object = $this->serverRepository->processServersData(true);
        $this->assertIsObject($object);
        $this->assertInstanceOf(ServerRepository::class, $object);
    }

    public function test_that_processServersData_filter_returns_array_no_cache(): void
    {
        $this->cacheManager = Mockery::mock(CacheManager::class);
        $this->filter = new Filter();
        $this->serverRepository = new ServerRepository($this->cacheManager, $this->filter);
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
        $this->cacheManager = Mockery::mock(CacheManager::class);
        $this->filter = new Filter();
        $this->cacheManager->expects()->get(Mockery::any(), CacheManager::SERVER_LIST_KEY)->andReturns([]);
        $this->cacheManager->expects()->set(Mockery::any(), CacheManager::SERVER_LIST_KEY, Mockery::any());

        $this->serverRepository = new ServerRepository($this->cacheManager, $this->filter);
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
        $this->filter = Mockery::mock(filter::class);
        $this->serverRepository = new ServerRepository($this->cacheManager, $this->filter);
        $result = $this->serverRepository->getLocationData(false);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function test_that_getLocationData_returns_array_with_cache(): void
    {
        $this->filter = Mockery::mock(filter::class);
        $this->serverRepository = new ServerRepository(new CacheManager, $this->filter);
        $result = $this->serverRepository->getLocationData(true);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

}
