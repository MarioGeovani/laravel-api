<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ServerRepositoryInterface
{
    public function processServersData(bool $useCache = false):Object;
    public function filter(Request $request):array;
    public function getLocationData(bool $useCache = false):array;
    public function import(Request $request);
}