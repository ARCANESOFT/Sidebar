<?php namespace Arcanesoft\Sidebar\Tests\Entities;

use Arcanesoft\Sidebar\Entities\Item;
use Arcanesoft\Sidebar\Tests\TestCase;

/**
 * Class     ItemTest
 *
 * @package  Arcanesoft\Sidebar\Tests\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ItemTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */
    /** @test */
    public function it_can_be_instantiated()
    {
        $item = $this->createItem($name = 'home', $title = 'Home', $this->baseUrl);

        $this->assertSame($name, $item->name());
        $this->assertSame($title, $item->title());
        $this->assertSame($this->baseUrl, $item->url());
        $this->assertSame('fa fa-fw fa-home', $item->icon());
        $this->assertFalse($item->isActive());

        $this->assertFalse($item->hasRoles());
        $this->assertEmpty($item->getRoles());
        $this->assertFalse($item->hasPermissions());
        $this->assertEmpty($item->getPermissions());

        $this->assertFalse($item->hasChildren());

        $children = $item->children();

        $this->assertInstanceOf(\Arcanesoft\Sidebar\Entities\ItemCollection::class, $children);
        $this->assertCount(0, $children);
    }

    /** @test */
    public function it_can_make_item()
    {
        $item = Item::make($name = 'home', $title = 'Home', $this->baseUrl, $icon = 'fa fa-fw fa-home');

        $this->assertSame($name, $item->name());
        $this->assertSame($title, $item->title());
        $this->assertSame($this->baseUrl, $item->url());
        $this->assertSame($icon, $item->icon());
        $this->assertFalse($item->isActive());

        $this->assertFalse($item->hasRoles());
        $this->assertEmpty($item->getRoles());
        $this->assertFalse($item->hasPermissions());
        $this->assertEmpty($item->getPermissions());

        $this->assertFalse($item->hasChildren());

        $children = $item->children();

        $this->assertInstanceOf(\Arcanesoft\Sidebar\Entities\ItemCollection::class, $children);
        $this->assertCount(0, $children);
    }

    /** @test */
    public function it_can_set_current_name_for_active_class()
    {
        $item = $this->createItem('home', 'Home', $this->baseUrl);

        $this->assertFalse($item->isActive());
        $this->assertEmpty($item->activeClass());

        $item->setCurrent('home');

        $this->assertTrue($item->isActive());
        $this->assertSame('active', $item->activeClass());
        $this->assertSame('is-active', $item->activeClass('is-active'));
    }

    /** @test */
    public function it_can_convert_to_array()
    {
        $item = $this->createItem('home', 'Home', $this->baseUrl);

        $this->assertInstanceOf(\Illuminate\Contracts\Support\Arrayable::class, $item);

        $expected = [
            'name'        => 'home',
            'title'       => 'Home',
            'url'         => 'http://localhost',
            'icon'        => 'fa fa-fw fa-home',
            'active'      => false,
            'roles'       => [],
            'permissions' => [],
            'children'    => [],
        ];

        $this->assertSame($expected, $item->toArray());
    }

    /** @test */
    public function it_can_convert_to_json()
    {
        $item = $this->createItem('home', 'Home', $this->baseUrl);

        $this->assertInstanceOf(\Illuminate\Contracts\Support\Jsonable::class, $item);
        $this->assertInstanceOf(\JsonSerializable::class, $item);

        $expected = json_encode($item->toArray(), 0);
        $this->assertJson($json = $item->toJson());
        $this->assertSame($expected, $json);

        $expected = json_encode($item, 0);

        $this->assertJson($json = $item->toJson());
        $this->assertSame($expected, $json);
    }

    /** @test */
    public function it_can_translate_titles()
    {
        $item = $this->createItem('home', 'sidebar::titles.home', $this->baseUrl);

        $this->assertSame('Home', $item->title());

        $this->app->setLocale('fr');

        $this->assertSame('Accueil', $item->title());
    }

    /** @test */
    public function it_can_add_children_without_auth_checks()
    {
        $item = $this->createItem('seo', 'SEO', '#');

        $this->assertFalse($item->hasChildren());
        $this->assertCount(0, $item->children());
        $this->assertEmpty($item->childrenClass());

        $item->addChildren([
            [
                'title'       => 'Statistics',
                'name'        => 'seo-dashboard',
                'route'       => 'sidebar::seo.stats',
                'icon'        => 'fa fa-fw fa-bar-chart',
            ],[
                'title'       => 'Pages',
                'name'        => 'seo-pages',
                'route'       => 'sidebar::seo.pages',
                'icon'        => 'fa fa-fw fa-files-o',
            ],
        ]);

        $this->assertTrue($item->hasChildren());
        $this->assertCount(2, $item->children());
        $this->assertSame('treeview', $item->childrenClass());
        $this->assertSame('sub-items', $item->childrenClass('sub-items'));
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */
    /**
     * Create a sidebar item.
     *
     * @param  string  $name
     * @param  string  $title
     * @param  string  $url
     * @param  array   $roles
     * @param  array   $permissions
     *
     * @return \Arcanesoft\Sidebar\Entities\Item
     */
    private function createItem($name, $title, $url, array $roles = [], array $permissions = [])
    {
        $item = new Item($name, $title, $url, $icon = 'fa fa-fw fa-home');

        $item->setRoles($roles);
        $item->setPermissions($permissions);

        return $item;
    }
}
