<?php

namespace Lucid\Anfix;

use Illuminate\Support\Facades\Facade;

class AnfixFacade extends Facade{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'anfix'; }
}