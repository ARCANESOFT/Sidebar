<?php namespace Arcanesoft\Sidebar\Tests;

use Arcanesoft\Sidebar\Item;
use Arcanesoft\Sidebar\Tests\Stubs\Models\User;

/**
 * Class     ItemTest
 *
 * @package  Arcanesoft\Sidebar\Tests
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

        static::assertSame($name, $item->name());
        static::assertSame($title, $item->title);
        static::assertSame($this->baseUrl, $item->url);
        static::assertNull($item->icon);
        static::assertFalse($item->isActive());

        static::assertFalse($item->hasRoles());
        static::assertEmpty($item->roles());
        static::assertFalse($item->hasPermissions());
        static::assertEmpty($item->permissions());

        static::assertFalse($item->hasChildren());

        $children = $item->children;

        static::assertInstanceOf(\Arcanesoft\Sidebar\Collection::class, $children);
        static::assertCount(0, $children);
    }

    /** @test */
    public function it_can_make_item()
    {
        $item = Item::make([
            'name'  => 'home',
            'title' => 'Home',
            'url'   => $this->baseUrl,
            'icon'  => 'fa fa-fw fa-home',
        ]);

        static::assertSame('home', $item->name());
        static::assertSame('Home', $item->title);
        static::assertSame($this->baseUrl, $item->url);
        static::assertSame('fa fa-fw fa-home', $item->icon);
        static::assertFalse($item->isActive());

        static::assertFalse($item->hasRoles());
        static::assertEmpty($item->roles());
        static::assertFalse($item->hasPermissions());
        static::assertEmpty($item->permissions());

        static::assertFalse($item->hasChildren());

        $children = $item->children;

        static::assertInstanceOf(\Arcanesoft\Sidebar\Collection::class, $children);
        static::assertCount(0, $children);
    }

    /** @test */
    public function it_can_set_current_name_for_active_class()
    {
        $item = $this->createItem('home', 'Home', $this->baseUrl);

        static::assertFalse($item->isActive());

        $item->setSelected('home');

        static::assertTrue($item->isActive());
        static::assertSame('active', $item->active());
        static::assertSame('is-active', $item->active('is-active'));
    }

    /** @test */
    public function it_can_convert_to_array()
    {
        $item = $this->createItem('home', 'Home', $this->baseUrl);

        static::assertInstanceOf(\Illuminate\Contracts\Support\Arrayable::class, $item);

        $expected = [
            'name'        => 'home',
            'title'       => 'Home',
            'url'         => $this->baseUrl,
            'icon'        => null,
            'active'      => false,
            'children'    => [],
            'roles'       => [],
            'permissions' => [],
        ];

        static::assertSame($expected, $item->toArray());
    }

    /** @test */
    public function it_can_convert_to_json()
    {
        $item = $this->createItem('home', 'Home', $this->baseUrl);

        static::assertInstanceOf(\Illuminate\Contracts\Support\Jsonable::class, $item);
        static::assertInstanceOf(\JsonSerializable::class, $item);

        $expected = json_encode($item->toArray(), 0);
        static::assertJson($json = $item->toJson());
        static::assertSame($expected, $json);

        $expected = json_encode($item, 0);

        static::assertJson($json = $item->toJson());
        static::assertSame($expected, $json);
    }

    /** @test */
    public function it_can_translate_titles()
    {
        $item = $this->createItem('home', 'sidebar::titles.home', $this->baseUrl);

        static::assertSame('Home', $item->title);

        $this->app->setLocale('fr');

        $item = $this->createItem('home', 'sidebar::titles.home', $this->baseUrl);

        static::assertSame('Accueil', $item->title);
    }

    /** @test */
    public function it_can_parse_urls()
    {
        // URL
        $item = Item::make(['name' => 'home', 'title' => 'Home', 'url' => '/']);

        static::assertSame($this->baseUrl, $item->url);

        // ROUTE
        $item = Item::make(['name' => 'contact', 'title' => 'Contact', 'route' => 'contact']);

        static::assertSame("{$this->baseUrl}/contact", $item->url);

        // ACTION
        $item = Item::make(['name' => 'contact', 'title' => 'Contact', 'action' => 'Arcanesoft\Sidebar\Tests\Stubs\Contrllers\PagesControler@contact']);

        static::assertSame("{$this->baseUrl}/contact", $item->url);
    }

    /** @test */
    public function it_can_push_child_items()
    {
        $item = $this->createItem('seo', 'SEO', '#');

        static::assertFalse($item->hasChildren());
        static::assertCount(0, $item->children);

        $item->addChildren([
            [
                'title'       => 'Statistics',
                'name'        => 'seo-dashboard',
                'route'       => 'sidebar::seo.stats',
                'icon'        => 'fa fa-fw fa-bar-chart',
            ],
            [
                'title'       => 'Pages',
                'name'        => 'seo-pages',
                'route'       => 'sidebar::seo.pages',
                'icon'        => 'fa fa-fw fa-files-o',
            ],
        ]);

        static::assertTrue($item->hasChildren());
        static::assertCount(2, $item->children);
    }

//    /** @test */
//    public function it_can_check_if_user_is_allowed_to_access_this_item()
//    {
//        $item = new SidebarItem([
//            'name' => 'seo',
//            'title' => 'SEO',
//            'url' => '/seo',
//            'roles' => ['seo-manager'],
//            'permissions' => ['seo.manage'],
//            'children' => [
//                [
//                    'title'       => 'Dashboard',
//                    'name'        => 'seo-dashboard',
//                    'url'         => 'seo/dashboard',
//                    'icon'        => 'fa fa-fw fa-bar-chart',
//                    'roles'       => ['seo-analyst'],
//                    'permissions' => ['seo.dashboard'],
//                ],
//            ]
//        ]);
//
//        // Admin
//        $this->beUser(true, false, false);
//
//        static::assertTrue($item->allowed());
//
//        // User has role
//        $this->beUser(false, $this->equalTo('seo-manager'), false);
//
//        static::assertTrue($item->allowed());
//
//        // User has permission
//        $this->beUser(false, false, $this->equalTo('seo.manage'));
//
//        static::assertTrue($item->allowed());
//
//        // User has role in children items
//        $this->beUser(false, $this->equalTo('seo-manager'), false);
//
//        static::assertTrue($item->allowed());
//
//        // User has permission in children items
//        $this->beUser(false, false, $this->equalTo('seo.dashboard'));
//
//        static::assertTrue($item->allowed());
//
//        // User (Without roles & permissions)
//        $this->beUser(false, false, false);
//
//        static::assertFalse($item->allowed());
//    }

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
     * @return \Arcanesoft\Sidebar\Item
     */
    private function createItem($name, $title, $url, array $roles = [], array $permissions = [])
    {
        return new Item(
            compact('name', 'title', 'url', 'roles', 'permissions')
        );
    }


    /**
     * Mock the authentication.
     *
     * @param  mixed  $isAdminReturn
     * @param  mixed  $hasRoleSlugReturn
     * @param  mixed  $mayReturn
     */
    private function beUser($isAdminReturn, $hasRoleSlugReturn, $mayReturn)
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn($isAdminReturn);
        $user->method('hasRoleSlug')->willReturn($hasRoleSlugReturn);
        $user->method('may')->willReturn($mayReturn);

        /** @var  \Illuminate\Foundation\Auth\User  $user */
        $this->be($user);
    }
}
