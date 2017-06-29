<?php namespace Arcanesoft\Sidebar\Tests\Entities;

use Arcanesoft\Sidebar\Entities\Item;
use Arcanesoft\Sidebar\Entities\ItemCollection;
use Arcanesoft\Sidebar\Tests\TestCase;

/**
 * Class     ItemCollectionTest
 *
 * @package  Arcanesoft\Sidebar\Tests\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ItemCollectionTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Test
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated()
    {
        $items = new ItemCollection;

        $this->assertCount(0, $items);
        $this->assertFalse($items->hasActiveItem());
    }

    /** @test */
    public function it_can_check_if_has_an_active_child()
    {
        $items = $this->makeItemCollection();

        $this->assertFalse($items->hasActiveItem());

        $items->setCurrent('home');

        $this->assertTrue($items->hasActiveItem());

        $items->setCurrent('blog');

        $this->assertFalse($items->hasActiveItem());
    }

    /** @test */
    public function it_can_convert_to_array_and_to_json()
    {
        $expected = [
            [
                'name'        => 'home',
                'title'       => 'Home',
                'url'         => '/',
                'icon'        => 'home-icon',
                'active'      => false,
                'roles'       => [],
                'permissions' => [],
                'children'    => [],
            ],
            [
                'name'        => 'contact',
                'title'       => 'CONTACT',
                'url'         => '/contact',
                'icon'        => 'contact-icon',
                'active'      => false,
                'roles'       => [],
                'permissions' => [],
                'children'    => []
            ],
        ];

        $this->assertEquals($expected, $this->makeItemCollection()->toArray());
        $this->assertEquals(json_encode($expected), $this->makeItemCollection()->toJson());
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make an item collection.
     *
     * @return \Arcanesoft\Sidebar\Entities\ItemCollection
     */
    private function makeItemCollection()
    {
        return tap(new ItemCollection, function (ItemCollection $items) {
            $items->push(Item::make('home', 'Home', '/', 'home-icon'));
            $items->push(Item::make('contact', 'CONTACT', '/contact', 'contact-icon'));
        });
    }
}
