<?php

namespace App\Enums;

enum DataFields: string
{
    case MODEL      = 'model';
    case RAM        = 'ram';
    case HDD        = 'hdd';
    case LOCATION   = 'location';
    case PRICE      = 'price';
}