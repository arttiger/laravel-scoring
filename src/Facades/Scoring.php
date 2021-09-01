<?php

namespace Arttiger\Scoring\Facades;

use Illuminate\Support\Facades\Facade;

class Scoring extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'scoring';
    }
}
