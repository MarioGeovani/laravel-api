<?php

namespace App\Enums;

enum FilterStorage: string
{
    case _0GB   = '0GB';
    case _250GB = '250GB';
    case _500GB = '500GB';
    case _1TB   = '1TB';
    case _2TB   = '2TB';
    case _3TB   = '3TB';
    case _4TB   = '4TB';
    case _8TB   = '8TB';
    case _12TB  = '12TB';
    case _24TB  = '24TB';
    case _48TB  = '48TB';
    case _72TB  = '72TB';

    public function capacity(): int
    {

        // IN GB
        return match($this) {
            self::_0GB => 0,
            self::_250GB => 250,
            self::_500GB => 500,
            self::_1TB => 1000,
            self::_2TB => 2000,
            self::_3TB => 3000,
            self::_4TB => 4000,
            self::_8TB => 8000,
            self::_12TB => 12000,
            self::_24TB => 24000,
            self::_48TB => 48000,
            self::_72TB => 72000,
        };
    }
}