<?php
namespace App\Classes;

use App\Enums\DataFields;
use App\Enums\FilterParams;

class Filter
{
    public function process(array $filterParams, array $data): array
    {
        $filterIdx = [];
        $filterResult = [];

        foreach($data as $key=>$item){
            if(array_key_exists(FilterParams::MAX_STORAGE->value, $filterParams)){
                if($this->isValidHddItem($item[DataFields::HDD->value], $filterParams[FilterParams::MAX_STORAGE->value], $filterParams[FilterParams::MIN_STORAGE->value])){
                    $filterIdx[0][] = $key;
                }
            }

            if(array_key_exists(FilterParams::RAM->value, $filterParams)){
                if($this->isValidRamItem($item[DataFields::RAM->value], $filterParams[FilterParams::RAM->value])){
                    $filterIdx[1][] = $key;
                }
            }

            if(array_key_exists(FilterParams::HDD_TYPE->value, $filterParams)){
                if($this->isValidHddTypeItem($item[DataFields::HDD->value], $filterParams[FilterParams::HDD_TYPE->value])){
                    $filterIdx[2][] = $key;
                }
            }

            if(array_key_exists(FilterParams::LOCATION->value, $filterParams)){
                if($this->isValidLocationItem($item[DataFields::LOCATION->value], $filterParams[FilterParams::LOCATION->value])){
                    $filterIdx[3][] = $key;
                }
            }
        }

        //if no results with a given filter no sense to show anything because the filters intercept
        if(array_key_exists(FilterParams::MAX_STORAGE->value, $filterParams) && empty($filterIdx[0])){
            $filterIdx[0] = [];
        }elseif(array_key_exists(FilterParams::RAM->value, $filterParams) && empty($filterIdx[1])){
            $filterIdx[1] = [];
        }elseif(array_key_exists(FilterParams::HDD_TYPE->value, $filterParams) && empty($filterIdx[2])){
            $filterIdx[2] = [];
        }elseif(array_key_exists(FilterParams::LOCATION->value, $filterParams) && empty($filterIdx[3])){
            $filterIdx[3] = [];
        }

        //No filters return all
        if(empty($filterIdx)){
            return [
                    'data' => $data,
                    'count'=> count($data)
                    ];
        }

        //intercept the idx between filter Results
        $filterIdxIntercepted = array_intersect(...$filterIdx);


        //final iteration to retrive data by IDX
        foreach($filterIdxIntercepted as $idx){
            $filterResult[] = $data[$idx];
        }

        return [
                'data' => $filterResult,
                'count'=> count($filterIdxIntercepted)
                ];
    }

    private function isValidHddItem(string $value, string $maxStorage, string $minStorage): bool{
        $isTB = false;
        $pos = strpos(strtoupper($value), 'GB');
        if(!$pos){
            $pos = strpos(strtoupper($value), 'TB');
            $isTB = true;
        }

        if(strpos($value, 'x')){
            $valuesToCalc = explode('x',substr($value,0, $pos));
            $result = $valuesToCalc[0] * $valuesToCalc[1];
        }else{
            $result = intval(substr($value, 0, 2));
        }

        //Need to convert from TB to GB to have the logic of measure
        if($isTB){
            $result = $result * 1000; // The value metrics will be always GB
        }

        $maxStorageValue = substr($maxStorage, 0, -2);
        $maxStorageMetric= substr($maxStorage, -2);

        if(strtoupper($maxStorageMetric) == 'TB'){
            $maxStorageValue = 1000 * $maxStorageValue;
        }

        $minStorageValue = substr($minStorage, 0, -2);
        $minStorageMetric= substr($minStorage, -2);

        if(strtoupper($minStorageMetric) == 'TB'){
            $minStorageValue = 1000 * $minStorageValue;
        }

        return  $result >= $minStorageValue &&
                $result <= $maxStorageValue;

    }

    private function isValidRamItem(string $value, array $ram): bool{

        $pos = strpos(strtoupper($value), 'GB');
        $formattedValue = substr($value,0, $pos + 2);

        foreach ($ram as $param) {
            if ($formattedValue === $param) {
                return true;
            }
        }
        return false;
    }

    private function isValidHddTypeItem(string $value, string $hddType): bool{

        $pos = strpos(strtoupper($value), $hddType);
        return $pos != false;
    }

    private function isValidLocationItem(string $value, string $location): bool{

        return trim(strtoupper($value)) == trim(strtoupper($location));
    }

}