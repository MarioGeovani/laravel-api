<?php

namespace App\Repositories;

use App\Cache\CacheManager;
use App\Classes\Filter;
use App\Interfaces\ServerRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ServerRepository implements ServerRepositoryInterface
{
    private array $data;
    private array $locationData;

    public function __construct(private readonly CacheManager $cache, private readonly Filter $filter)
    {}

    public function processServersData(bool $useCache = false): Object
    {
        if($useCache){
            $cachedData = $this->cache->get('', CacheManager::SERVER_LIST_KEY);
            if (! empty($cachedData)) {
                $this->data = $cachedData;
            }else{
                $this->loadData();
                $this->cache->set('', CacheManager::SERVER_LIST_KEY, $this->data);
            }
        }else{
            $this->loadData();
        }

        return $this;
    }

    public function filter(Request $request): array
    {
        return $this->filter->process($request->validated(), $this->data);
    }

    public function getLocationData(bool $useCache = false): array
    {
        if($useCache){
            $cachedData = $this->cache->get('', CacheManager::LOCATION_LIST_KEY);
            if (! empty($cachedData)) {
                return $cachedData;
            }else{
                $this->loadData();
                $this->cache->set('', CacheManager::LOCATION_LIST_KEY, $this->locationData);
            }
        }else{
            $this->loadData();
        }

        return $this->locationData;
    }

    public function import(Request $request) : void
    {
        $uploadFolder = Config::get('import.upload_folder');
        $newFileName = Config::get('import.imported_file_name');
        $image = $request->file('file', []);
        $image->storeAs(storage_path($uploadFolder), $newFileName, ['disk' => 'local']);

        //Parse info to cache
        $import =$this->getExcelData();
        //Always set to cache on import
        $this->cache->set('', CacheManager::SERVER_LIST_KEY, $import->getAllDataArray());
        $this->cache->set('', CacheManager::LOCATION_LIST_KEY, $import->getLocationDataArray());
    }

    private function getExcelData(): DataImport
    {
        $uploadFolder = Config::get('import.upload_folder');
        $newFileName = Config::get('import.imported_file_name');
        $import = new DataImport;
        Excel::import($import, storage_path($uploadFolder) . '/' . $newFileName);
        return $import;
    }

    private function loadData(): void
    {
        $import = $this->getExcelData();
        $this->data = $import->getAllDataArray();
        $this->locationData = $import->getLocationDataArray();
    }
}