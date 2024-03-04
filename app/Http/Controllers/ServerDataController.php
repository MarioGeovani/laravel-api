<?php

namespace App\Http\Controllers;

use App\Enums\FilterHardDisckType;
use App\Enums\FilterParams;
use App\Enums\FilterRam;
use App\Enums\FilterStorage;
use App\Enums\FilterType;
use App\Http\Requests\FilterRequest;
use App\Interfaces\ServerRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

class ServerDataController extends Controller
{
    public function __construct(private readonly ServerRepositoryInterface $serverRepository)
    {}

    // Get server list and filter by the request
    public function list(FilterRequest $request): JsonResponse
    {
        return response()->json(
                                $this->serverRepository->processServersData(true)->filter($request),
                                HttpResponse::HTTP_OK
                            );
    }

    // Gets Filters informationto help on /list request
    public function filters() : JsonResponse{
        return response()->json([
                                    FilterParams::MAX_STORAGE->value  => [ 'type'=> FilterType::RANGE_SLIDER, 'values' => FilterStorage::cases()] ,
                                    FilterParams::MIN_STORAGE->value  => [ 'type'=> FilterType::RANGE_SLIDER, 'values' => FilterStorage::cases()] ,
                                    FilterParams::RAM->value      => [ 'type'=> FilterType::CHECKBOX, 'values' => FilterRam::cases()],
                                    FilterParams::HDD_TYPE->value => [ 'type'=> FilterType::DROPDOWN, 'values' => FilterHardDisckType::cases()],
                                    FilterParams::LOCATION->value => [ 'type'=> FilterType::DROPDOWN, 'values' => $this->serverRepository->getLocationData(true)]
                                ],
                                HttpResponse::HTTP_OK
                            );
    }

    // Imports new excel file and set data to Cache
    public function import(Request $request): JsonResponse
    {
        $this->serverRepository->import($request);
        return response()->json(['status' => 'success'], HttpResponse::HTTP_OK);
    }
}
