<?php

namespace App\Enums;

enum FilterParams: string
{
    case MIN_STORAGE   = 'min_storage';
    case MAX_STORAGE   = 'max_storage';
    case HDD_TYPE      = 'hdd_type';
    case RAM           = 'ram';
    case LOCATION      = 'location';
}