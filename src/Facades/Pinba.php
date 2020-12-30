<?php

namespace Chocofamilyme\LaravelPinba\Facades;

use Chocofamilyme\LaravelPinba\Profiler\ProfilerInterface;
use Illuminate\Support\Facades\Facade;

class Pinba extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ProfilerInterface::class;
    }
}
