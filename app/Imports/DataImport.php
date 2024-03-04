<?php

namespace App\Imports;

use App\Enums\DataFields;
use Maatwebsite\Excel\Concerns\ToArray;


class DataImport implements ToArray
{
    private array $allData;
    private array $locationData;

    public function __construct()
    {
        $this->allData = [];
        $this->locationData = [];
    }

    public function array(array $rows)
    {
        foreach ($rows as $key=>$row) {
            if($key==0){
                continue;
            }

            $this->allData[] = array(
                                    DataFields::MODEL->value    => $row[0],
                                    DataFields::RAM->value      => $row[1],
                                    DataFields::HDD->value      => $row[2],
                                    DataFields::LOCATION->value => $row[3],
                                    DataFields::PRICE->value    => $row[4],
                                );

            $this->locationData[] = $row[3];
        }
    }

    public function getAllDataArray(): array
    {
        return $this->allData;
    }

    public function getLocationDataArray(): array
    {
        return array_values(array_unique($this->locationData));
    }
}
