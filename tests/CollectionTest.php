<?php namespace Arcanesoft\Sidebar\Tests;

use Arcanesoft\Sidebar\Collection;

/**
 * Class     CollectionTest
 *
 * @package  Arcanesoft\Sidebar\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CollectionTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Test
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated()
    {
        $items = new Collection;

        static::assertCount(0, $items);
        static::assertFalse($items->hasAnySelected());
    }

    /** @test */
    public function it_can_check_if_has_an_active_child()
    {
        $items = $this->makeItemCollection();

        static::assertFalse($items->hasAnySelected());

        $items->setSelected('home');

        static::assertTrue($items->hasAnySelected());

        $items->setSelected('blog');

        static::assertFalse($items->hasAnySelected());
    }

    /** @test */
    public function it_can_convert_to_array_and_to_json()
    {
        $expected = [
            [
                'name'        => 'home',
                'title'       => 'Home',
                'url'         => $this->baseUrl,
                'icon'        => 'home-icon',
                'active'      => false,
                'children'    => [],
                'roles'       => [],
                'permissions' => [],
            ],
            [
                'name'        => 'contact',
                'title'       => 'Contact',
                'url'         => "{$this->baseUrl}/contact",
                'icon'        => 'contact-icon',
                'active'      => false,
                'children'    => [],
                'roles'       => [],
                'permissions' => [],
            ],
        ];

        static::assertEquals($expected, $this->makeItemCollection()->toArray());
        static::assertEquals(json_encode($expected), $this->makeItemCollection()->toJson());
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make an item collection.
     *
     * @return \Arcanesoft\Sidebar\Collection
     */
    private function makeItemCollection()
    {
        return tap(new Collection, function (Collection $items) {
            $items->addItem(['name' => 'home', 'title' => 'Home', 'url' => '/', 'icon' => 'home-icon']);
            $items->addItem(['name' => 'contact', 'title' => 'Contact', 'url' => '/contact', 'icon' => 'contact-icon']);
        });
    }
}
