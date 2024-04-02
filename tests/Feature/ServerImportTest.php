<?php

namespace Tests\Feature;

use app\Cache\CacheManager;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\MockInterface;
use Tests\TestCase;

class ServerImportTest extends TestCase
{
    use WithFaker;

    private MockInterface $serverRepository;
    private string $testUploadDir = 'test-upload';
    private string $testUploadFile = 'the_test_upload_file.xlsx';

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();

        //define test data
        Config::set('import.upload_folder', $this->testUploadDir);
        Config::set('import.imported_file_name', $this->testUploadFile);

        Storage::fake();
        Excel::fake();
    }

    public function test_the_import_is_successful(): void
    {
        //File to be Uploaded
        $file = UploadedFile::fake()->create(
            storage_path('test-files/the_file_test_to_import.xlsx'),
            'the_file_test_to_import.xlsx',
            'application/vnd.ms-excel.sheet.macroenabled.12',
            null,
            true
        );

        $response = $this->post(route('server.file.import'), ['file' => $file]);
        $response->isOk();

        Storage::fake()->exists(storage_path($this->testUploadFile));

        $this->assertTrue(Cache::has(CacheManager::SERVER_LIST_KEY . '_'));
    }

    public function test_the_import_is_not_successful(): void
    {
       //create invalid file to be Uploaded
        $file = UploadedFile::fake()->create('invalid_document_to_upload.xls', 100);

        $response = $this->post(route('server.file.import'), ['file' => $file]);

        $response->assertUnprocessable();

        $exist = Storage::fake()->exists(storage_path($this->testUploadFile));
        $this->assertFalse($exist);
    }

}
