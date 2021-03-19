<?php

namespace TCGunel\XmlAligner\Facades;

use Illuminate\Support\Facades\Facade;

class XmlAligner extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'xmlAligner';
    }
}
