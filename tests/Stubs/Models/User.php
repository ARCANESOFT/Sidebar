<?php namespace Arcanesoft\Sidebar\Tests\Stubs\Models;

use Arcanesoft\Contracts\Auth\Models\User as UserContract;
use Illuminate\Foundation\Auth\User as BaseUser;

/**
 * Class     User
 *
 * @package  Arcanesoft\Sidebar\Tests\Stubs\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class User extends BaseUser implements UserContract
{
    //
}
