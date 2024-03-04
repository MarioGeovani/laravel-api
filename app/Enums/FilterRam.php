<?php

namespace App\Enums;

enum FilterRam: string
{
    case _2B = '2GB';
    case _4GB = '4GB';
    case _8GB = '8GB';
    case _12GB = '12GB';
    case _16GB = '16GB';
    case _24GB = '24GB';
    case _32GB = '32GB';
    case _48GB = '48GB';
    case _64GB = '64GB';
    case _96GB = '96GB';

    public static function getAllValues(): array
    {
        return array_column(FilterRam::cases(), 'value');
    }
}