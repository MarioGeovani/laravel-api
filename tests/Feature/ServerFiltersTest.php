<?php

namespace Tests\Feature;

use App\Enums\FilterHardDisckType;
use App\Enums\FilterParams;
use App\Enums\FilterRam;
use App\Enums\FilterStorage;
use App\Enums\FilterType;
use App\Repositories\ServerRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Tests\TestCase;

class ServerFiltersTest extends TestCase
{
    use WithFaker;

    private MockInterface $serverRepository;
    private array $locationMockedData;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();
        $this->locationMockedData = $this->locationMockedData();

        $this->serverRepository = $this->Mock(ServerRepository::class);
        $this->serverRepository->expects()->getLocationData(true)->andReturns($this->locationMockedData);

    }
    /**
     *
     */
    public function test_the_route_returns_a_successful_response(): void
    {
        $expectedResponse =  [
                FilterParams::MAX_STORAGE->value  => [ 'type'=> FilterType::RANGE_SLIDER, 'values' => FilterStorage::cases()] ,
                FilterParams::MIN_STORAGE->value  => [ 'type'=> FilterType::RANGE_SLIDER, 'values' => FilterStorage::cases()] ,
                FilterParams::RAM->value      => [ 'type'=> FilterType::CHECKBOX, 'values' => FilterRam::cases()],
                FilterParams::HDD_TYPE->value => [ 'type'=> FilterType::DROPDOWN, 'values' => FilterHardDisckType::cases()],
                FilterParams::LOCATION->value => [ 'type'=> FilterType::DROPDOWN, 'values' => $this->locationMockedData]
        ];

         $response = $this->get(route('filters.list'));
         $response->assertJsonIsObject()->isOk();

         $this->assertEquals($response->original, $expectedResponse);
    }

    private function locationMockedData(): array
    {
        $numCountries = rand(1, 10);

        $countries = [];
        for ($i = 0; $i < $numCountries; $i++) {
            $countries[] = $this->faker->country();
        }

        return $countries;
    }
}
