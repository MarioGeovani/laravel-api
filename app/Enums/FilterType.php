<?php

namespace App\Enums;

enum FilterType: string
{
    case RANGE_SLIDER   = 'RANGE_SLIDER';
    case CHECKBOX       = 'CHECKBOX';
    case DROPDOWN       = 'DROPDOWN';
}