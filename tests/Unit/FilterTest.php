<?php

namespace Tests\Unit;

use App\Classes\Filter;
use App\Imports\DataImport;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\MockInterface;
use Tests\TestCase;

class FilterTest extends TestCase
{
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


    public function test_that_process_filter_returns_array(): void
    {
        $this->filter = new Filter();
        $result = $this->filter->process([], $this->getTestExcelData());

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertEquals(8 , $result['count']);
    }

    private function getTestExcelData(): array
    {
        $import = new DataImport;
        Excel::import($import, storage_path($this->testUploadDir) . '/' . $this->testUploadFile);
        return $import->getAllDataArray();
    }

}
