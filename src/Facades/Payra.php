<?php
namespace Payra\Facades;

use Illuminate\Support\Facades\Facade;

class Payra extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Payra\Payra::class;
    }
}
