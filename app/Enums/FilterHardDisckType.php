<?php

namespace App\Enums;

enum FilterHardDisckType: string
{
    case SAS    = 'SAS';
    case SATA   = 'SATA';
    case SSD    = 'SSD';
}