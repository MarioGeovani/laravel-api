<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ServerListTest extends TestCase
{
    use WithFaker;

    //Im setting a different dir from the real upload to not mess around the real server data
    private string $testUploadDir = 'test-upload';
    private string $testUploadFile = 'the_test_upload_file.xlsx';

    public function setUp(): void
    {
        parent::setUp();

         //define test data
         Config::set('import.upload_folder', $this->testUploadDir);
         Config::set('import.imported_file_name', $this->testUploadFile);

        $this->withoutMiddleware();

        //Import file for all tests
        $this->importTestFile();

    }

    public function test_the_route_returns_a_full_list(): void
    {
        $response = $this->get(route('server.data.list'));
        $response->assertJsonIsObject()->isOk();

        $this->assertArrayHasKey('data', $response->original);
        $this->assertArrayHasKey('count', $response->original);
        $this->assertEquals(count($this->expectedFullListResult()), $response->original['count']);
        $this->assertEquals($this->expectedFullListResult(), $response->original['data']);
    }

    /**
     * @dataProvider provideFilterParamScenarios
     *
     * @param array $filterParams
     * @param array $expectedResult
     *
     * */
    public function test_the_route_returns_a_server_list_filtered(array $filterParams, array $expectedResult): void
    {
        $response = $this->get(route('server.data.list', $filterParams));
        $response->assertJsonIsObject()->isOk();
        $this->assertEquals($expectedResult, $response->original);
    }


      /**
     * @dataProvider provideFilterParamValidationErrorScenarios
     *
     * @param array $filterParams
     * @param array $expectedResult
     *
     * */
    public function test_the_route_returns_filter_validation_errors(array $filterParams, array $expectedResult): void
    {
        $this->expectException(ValidationException::class);
        $response = $this->get(route('server.data.list', $filterParams));
        $response->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals($errors->default->messages(), $expectedResult);

        $response->assertJsonIsObject();
        $response->assertInvalid();

    }

    public static function provideFilterParamScenarios(): array
    {
        return [
            'by storage'     => [
                ['min_storage' => '4TB' , 'max_storage'=> '8TB'],
                ['data' => [
                            [
                                'model' => 'Dell R210Intel Xeon X3440 Test',
                                'ram' => '4GBDDR3',
                                'hdd' => '2x4TBSATA2',
                                'location' => 'AmsterdamAMS-01',
                                'price' => '€49.99'
                            ],
                            [
                                'model' => 'Dell R210Intel Xeon X3440 Test 2',
                                'ram' => '4GBDDR3',
                                'hdd' => '2x4TBSATA2',
                                'location' => 'AnotherAmsterdamAMS-02',
                                'price' => '€449.99'
                            ]
                        ],
                'count' => 2
                ]
            ],
            'by ram'     => [
                ['ram[]' => '8GB', 'ram[]'=> '32GB'],
                ['data' => [
                        [
                            'model' => 'HP DL380eG82x Intel Xeon E5-2420 Test',
                            'ram' => '32GBDDR3',
                            'hdd' => '8x16TBSATA2',
                            'location' => 'AmsterdamAMS-01',
                            'price' => '€131.99'
                        ],
                        [
                            'model' => 'HP DL380eG82x Intel Xeon E5-2420 Test 2',
                            'ram' => '32GBDDR3',
                            'hdd' => '8x16TBSATA2',
                            'location' => 'AnotherAmsterdamAMS-02',
                            'price' => '€1131.99'
                        ]
                    ],
                'count' => 2
                ]
            ],
            'by hdd_type'=> [
                ['hdd_type' => 'SSD'],
                ['data' => [
                        [
                            'model' => 'RH2288v32x Intel Xeon E5-2650V4 Test',
                            'ram' => '96GBDDR4',
                            'hdd' => '4x480GBSSD',
                            'location' => 'AmsterdamAMS-01',
                            'price' => '€227.99'
                        ],
                        [
                            'model' => 'RH2288v32x Intel Xeon E5-2650V4 Test 2',
                            'ram' => '96GBDDR4',
                            'hdd' => '4x480GBSSD',
                            'location' => 'AnotherAmsterdamAMS-02',
                            'price' => '€2227.99'
                        ]
                    ],
                    'count' => 2
                ]
            ],
            'by location'=> [
                ['location' => 'AmsterdamAMS-01'],
                ['data' => [
                        [
                            'model' => 'Dell R210Intel Xeon X3440 Test',
                            'ram' => '4GBDDR3',
                            'hdd' => '2x4TBSATA2',
                            'location' => 'AmsterdamAMS-01',
                            'price' => '€49.99'
                        ],
                        [
                            'model' => 'HP DL180G62x Intel Xeon E5620 Test',
                            'ram' => '8GBDDR3',
                            'hdd' => '8x8TBSATA2',
                            'location' => 'AmsterdamAMS-01',
                            'price' => '€119.00'
                        ],
                        [
                            'model' => 'HP DL380eG82x Intel Xeon E5-2420 Test',
                            'ram' => '32GBDDR3',
                            'hdd' => '8x16TBSATA2',
                            'location' => 'AmsterdamAMS-01',
                            'price' => '€131.99'
                        ],
                        [
                            'model' => 'RH2288v32x Intel Xeon E5-2650V4 Test',
                            'ram' => '96GBDDR4',
                            'hdd' => '4x480GBSSD',
                            'location' => 'AmsterdamAMS-01',
                            'price' => '€227.99'
                        ]
                    ],
                    'count' => 4
                ]
            ],
            'by hdd && ram && hdd '=> [
                ['min_storage' => '4TB' , 'max_storage'=> '8TB', 'ram[]' => '4GB', 'hdd_type' => 'SATA'],
                ['data' => [
                        [
                            'model' => 'Dell R210Intel Xeon X3440 Test',
                            'ram' => '4GBDDR3',
                            'hdd' => '2x4TBSATA2',
                            'location' => 'AmsterdamAMS-01',
                            'price' => '€49.99'
                        ],
                        [
                            'model' => 'Dell R210Intel Xeon X3440 Test 2',
                            'ram' => '4GBDDR3',
                            'hdd' => '2x4TBSATA2',
                            'location' => 'AnotherAmsterdamAMS-02',
                            'price' => '€449.99'
                        ]
                    ],
                    'count' => 2
                ]
            ],
            'by hdd && ram && hdd && location'=> [
                ['min_storage' => '4TB' , 'max_storage'=> '8TB', 'ram[]' => '4GB', 'hdd_type' => 'SATA', 'location' => 'AnotherAmsterdamAMS-02'],
                ['data' => [
                        [
                            'model' => 'Dell R210Intel Xeon X3440 Test 2',
                            'ram' => '4GBDDR3',
                            'hdd' => '2x4TBSATA2',
                            'location' => 'AnotherAmsterdamAMS-02',
                            'price' => '€449.99'
                        ]
                    ],
                    'count' => 1
                ]
            ],
            'by filter to no results'=> [
                ['min_storage' => '500GB' , 'max_storage'=> '500GB', 'ram[]' => '4GB', 'hdd_type' => 'SATA', 'location' => 'AnotherAmsterdamAMS-01'],
                ['data' => [],
                    'count' => 0
                ]
            ]
        ];
    }

    public static function provideFilterParamValidationErrorScenarios(): array
    {
        return [
            'by min_storage invalid'     => [
                ['min_storage' => '0TB'],
                [
                    'max_storage' =>  ['The max storage field is required when min storage is present.'],
                    'min_storage' => ['The selected min storage is invalid.']
                ]
            ],
            'by max_storage invalid'     => [
                ['max_storage' => '0TB'],
                [
                    'min_storage' =>  ['The min storage field is required when max storage is present.'],
                    'max_storage' => ['The selected max storage is invalid.']
                ]
            ],
            'by ram invalid'     => [
                ['ram[]' => '0GB', 'ram[]' => '100GB'],
                [
                    'ram.0' => ['The selected ram.0 is invalid.']
                ]
            ],
            'by hdd_type invalid'     => [
                ['hdd_type' => 'DDS'],
                [
                    'hdd_type' => ['The selected hdd type is invalid.']
                ]
            ],
        ];
    }

    private function importTestFile(){
        //File to be Uploaded
        $file = UploadedFile::fake()->createWithContent('the_file_test_to_import.xlsx',
            Storage::disk('local')->get(storage_path('test-files-server-import-test/the_file_test_to_import.xlsx'))
        );

        $this->post(route('server.file.import'), ['file' => $file]);
    }

    private function expectedFullListResult():array {
        return [
            [
                'model' => 'Dell R210Intel Xeon X3440 Test',
                'ram' => '4GBDDR3',
                'hdd' => '2x4TBSATA2',
                'location' => 'AmsterdamAMS-01',
                'price' => '€49.99'
            ],
            [
                'model' => 'HP DL180G62x Intel Xeon E5620 Test',
                'ram' => '8GBDDR3',
                'hdd' => '8x8TBSATA2',
                'location' => 'AmsterdamAMS-01',
                'price' => '€119.00'
            ],
            [
                'model' => 'HP DL380eG82x Intel Xeon E5-2420 Test',
                'ram' => '32GBDDR3',
                'hdd' => '8x16TBSATA2',
                'location' => 'AmsterdamAMS-01',
                'price' => '€131.99'
            ],
            [
                'model' => 'RH2288v32x Intel Xeon E5-2650V4 Test',
                'ram' => '96GBDDR4',
                'hdd' => '4x480GBSSD',
                'location' => 'AmsterdamAMS-01',
                'price' => '€227.99'
            ],
            [
                'model' => 'Dell R210Intel Xeon X3440 Test 2',
                'ram' => '4GBDDR3',
                'hdd' => '2x4TBSATA2',
                'location' => 'AnotherAmsterdamAMS-02',
                'price' => '€449.99'
            ],
            [
                'model' => 'HP DL180G62x Intel Xeon E5620 Test 2',
                'ram' => '8GBDDR3',
                'hdd' => '8x8TBSATA2',
                'location' => 'AnotherAmsterdamAMS-02',
                'price' => '€1119.00'
            ],
            [
                'model' => 'HP DL380eG82x Intel Xeon E5-2420 Test 2',
                'ram' => '32GBDDR3',
                'hdd' => '8x16TBSATA2',
                'location' => 'AnotherAmsterdamAMS-02',
                'price' => '€1131.99'
            ],
            [
                'model' => 'RH2288v32x Intel Xeon E5-2650V4 Test 2',
                'ram' => '96GBDDR4',
                'hdd' => '4x480GBSSD',
                'location' => 'AnotherAmsterdamAMS-02',
                'price' => '€2227.99'
            ]
        ];
    }


}
