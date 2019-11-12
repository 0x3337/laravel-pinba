<?php

namespace Chocofamilyme\LaravelPinba\Facades;

use Illuminate\Support\Facades\Facade;

class Pinba extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pinba';
    }
}