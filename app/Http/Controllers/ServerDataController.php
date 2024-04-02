<?php

namespace App\Http\Controllers;

use App\Enums\FilterHardDisckType;
use App\Enums\FilterParams;
use App\Enums\FilterRam;
use App\Enums\FilterStorage;
use App\Enums\FilterType;
use App\Http\Requests\FilterRequest;
use App\Interfaces\ServerRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

class ServerDataController extends Controller
{
    public function __construct(private readonly ServerRepositoryInterface $serverRepository)
    {}


     /**
     *  Get server list and filter by the request
     *
     * @param  \Requests\FilterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function list(FilterRequest $request): JsonResponse
    {
        try{
            return response()->json(
                                        $this->serverRepository->processServersData(true)->filter($request),
                                        HttpResponse::HTTP_OK
                                    );
        }catch(Exception){
            return response()->json([], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


     /**
     *  Gets Filters information to help on /servers endpoint
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filters() : JsonResponse{

        try{
            $response = [
                FilterParams::MAX_STORAGE->value  => [ 'type'=> FilterType::RANGE_SLIDER, 'values' => FilterStorage::cases()] ,
                FilterParams::MIN_STORAGE->value  => [ 'type'=> FilterType::RANGE_SLIDER, 'values' => FilterStorage::cases()] ,
                FilterParams::RAM->value      => [ 'type'=> FilterType::CHECKBOX, 'values' => FilterRam::cases()],
                FilterParams::HDD_TYPE->value => [ 'type'=> FilterType::DROPDOWN, 'values' => FilterHardDisckType::cases()],
                FilterParams::LOCATION->value => [ 'type'=> FilterType::DROPDOWN, 'values' => $this->serverRepository->getLocationData(true)]
            ];

            return response()->json($response, HttpResponse::HTTP_OK);
        }catch(Exception){
            return response()->json([], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     *  Imports new excel file and set data to Cache
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request): JsonResponse
    {
        //The FormRequest Doesnt deal very well with the upload files validation

        if($request->file('file', null)?->getClientOriginalExtension() != 'xlsx'){
            return response()->json([], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $this->serverRepository->import($request);
            return response()->json([], HttpResponse::HTTP_OK);
        }catch(Exception){
            return response()->json([] , HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
