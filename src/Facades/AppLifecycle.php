<?php

namespace Djurovicigoor\AppLifecycle\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed execute(array $options = [])
 * @method static object|null getStatus()
 *
 * @see \Djurovicigoor\AppLifecycle\AppLifecycle
 */
class AppLifecycle extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Djurovicigoor\AppLifecycle\AppLifecycle::class;
    }
}