<?php namespace Arcanesoft\Sidebar\Facades;

use Arcanesoft\Sidebar\Contracts\Manager as ManagerContract;
use Illuminate\Support\Facades\Facade;

/**
 * Class     Sidebar
 *
 * @package  Arcanesoft\Sidebar\Facades
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Sidebar extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return ManagerContract::class; }
}
