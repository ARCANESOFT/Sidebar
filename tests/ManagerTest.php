<?php namespace Arcanesoft\Sidebar\Tests;

/**
 * Class     ManagerTest
 *
 * @package  Arcanesoft\Sidebar\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ManagerTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanesoft\Sidebar\Contracts\Manager */
    private $manager;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp()
    {
        parent::setUp();

        $this->manager = $this->app->make(\Arcanesoft\Sidebar\Contracts\Manager::class);
    }

    public function tearDown()
    {
        unset($this->manager);

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */
    /** @test */
    public function it_can_be_instantiated()
    {
        $expectations = [
            \Arcanesoft\Sidebar\Contracts\Manager::class,
            \Arcanesoft\Sidebar\Manager::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->manager);
        }

        static::assertFalse($this->manager->hasItems());
        static::assertCount(0, $this->manager->items());
    }

    /** @test */
    public function it_can_add_item_with_route()
    {
        $this->manager->add($item = [
            'name'        => 'seo',
            'title'       => 'SEO',
            'url'         => 'http://localhost/seo',
            'icon'        => 'seo-icon',
            'active'      => false,
            'children'    => [],
            'roles'       => [],
            'permissions' => [],
        ]);

        static::assertEquals([$item], $this->manager->items()->toArray());
    }

    /** @test */
    public function it_can_load_sidebar_items_from_config()
    {
        $this->fakeSidebarItemsConfig();

        static::assertFalse($this->manager->hasItems());

        $this->manager->loadFromConfig('testing.sidebar.menus');

        static::assertTrue($this->manager->hasItems());
        static::assertCount(2, $this->manager->items());
    }

    /** @test */
    public function it_can_load_sidebar_items_from_array()
    {
        static::assertCount(0, $this->manager->items());

        $this->manager->loadFromArray([
            [
                'name'     => 'home',
                'title'    => 'HOME',
                'url'      => '/home',
                'icon'     => 'home-icon',
            ],
            [
                'name'     => 'blog',
                'title'    => 'BLOG',
                'url'      => '/blog',
                'icon'     => 'blog-icon',
                'roles'    => ['blog-manager'],
                'children' => [
                    [
                        'name'        => 'blog-posts',
                        'title'       => 'POSTS',
                        'url'         => '/blog/posts',
                        'icon'        => 'posts-icon',
                        'roles'       => [],
                        'permissions' => ['blog.posts.crud']
                    ],
                    [
                        'name'        => 'blog-categories',
                        'title'       => 'CATEGORIES',
                        'url'         => '/blog/categories',
                        'icon'        => 'categories-icon',
                        'roles'       => [],
                        'permissions' => ['blog.categories.crud']
                    ],
                ],
            ],
        ]);

        static::assertCount(2, $this->manager->items());

        $expected = [
            [
                'name'        => 'home',
                'title'       => 'HOME',
                'url'         => "{$this->baseUrl}/home",
                'icon'        => 'home-icon',
                'active'      => false,
                'children'    => [],
                'roles'       => [],
                'permissions' => [],
            ],
            [
                'name'        => 'blog',
                'title'       => 'BLOG',
                'url'         => "{$this->baseUrl}/blog",
                'icon'        => 'blog-icon',
                'active'      => false,
                'children'    => [
                    [
                        'name'        => 'blog-posts',
                        'title'       => 'POSTS',
                        'url'         => "{$this->baseUrl}/blog/posts",
                        'icon'        => 'posts-icon',
                        'active'      => false,
                        'children'    => [],
                        'roles'       => [],
                        'permissions' => [
                            'blog.posts.crud',
                        ],
                    ],
                    [
                        'name'        => 'blog-categories',
                        'title'       => 'CATEGORIES',
                        'url'         => "{$this->baseUrl}/blog/categories",
                        'icon'        => 'categories-icon',
                        'active'      => false,
                        'children'    => [],
                        'roles'       => [],
                        'permissions' => [
                            'blog.categories.crud',
                        ],
                    ],
                ],
                'roles'       => [
                    'blog-manager',
                ],
                'permissions' => [],
            ],
        ];

        static::assertEquals($expected, $this->manager->items()->toArray());
    }

    /** @test */
    public function it_can_set_selected_sidebar_item()
    {
        $this->fakeSidebarItemsConfig();

        $this->manager->loadFromConfig('testing.sidebar.menus');

        static::assertTrue($this->manager->hasItems());

        $expected = [
            [
                'name'        => 'home',
                'title'       => 'HOME',
                'url'         => "{$this->baseUrl}/home",
                'icon'        => 'home-icon',
                'active'      => true,
                'children'    => [],
                'roles'       => [],
                'permissions' => [],
            ],
            [
                'name'        => 'blog',
                'title'       => 'BLOG',
                'url'         => "{$this->baseUrl}/blog",
                'icon'        => 'blog-icon',
                'active'      => false,
                'children'    => [
                    [
                        'name'        => 'blog-posts',
                        'title'       => 'POSTS',
                        'url'         => "{$this->baseUrl}/blog/posts",
                        'icon'        => 'posts-icon',
                        'active'      => false,
                        'children'    => [],
                        'roles'       => [],
                        'permissions' => [
                            'blog.posts.crud',
                        ],
                    ],
                    [
                        'name'        => 'blog-categories',
                        'title'       => 'CATEGORIES',
                        'url'         => "{$this->baseUrl}/blog/categories",
                        'icon'        => 'categories-icon',
                        'active'      => false,
                        'children'    => [],
                        'roles'       => [],
                        'permissions' => [
                            'blog.categories.crud',
                        ],
                    ],
                ],
                'roles'       => [
                    'blog-manager',
                ],
                'permissions' => [],
            ],
        ];

        $this->manager->setSelectedItem('home');

        static::assertEquals($expected, $this->manager->items()->toArray());

        $expected = [
            [
                'name'        => 'home',
                'title'       => 'HOME',
                'url'         => "{$this->baseUrl}/home",
                'icon'        => 'home-icon',
                'active'      => false,
                'children'    => [],
                'roles'       => [],
                'permissions' => [],
            ],
            [
                'name'        => 'blog',
                'title'       => 'BLOG',
                'url'         => "{$this->baseUrl}/blog",
                'icon'        => 'blog-icon',
                'active'      => true,
                'children'    => [
                    [
                        'name'        => 'blog-posts',
                        'title'       => 'POSTS',
                        'url'         => "{$this->baseUrl}/blog/posts",
                        'icon'        => 'posts-icon',
                        'active'      => false,
                        'children'    => [],
                        'roles'       => [],
                        'permissions' => [
                            'blog.posts.crud',
                        ],
                    ],
                    [
                        'name'        => 'blog-categories',
                        'title'       => 'CATEGORIES',
                        'url'         => "{$this->baseUrl}/blog/categories",
                        'icon'        => 'categories-icon',
                        'active'      => true,
                        'children'    => [],
                        'roles'       => [],
                        'permissions' => [
                            'blog.categories.crud',
                        ],
                    ],
                ],
                'roles'       => [
                    'blog-manager',
                ],
                'permissions' => [],
            ],
        ];

        $this->manager->setSelectedItem('blog-categories');

        static::assertEquals($expected, $this->manager->items()->toArray());
    }

    /** @test */
    public function it_can_show_and_hide_sidebar()
    {
        static::assertTrue($this->manager->isShown());

        $this->manager->hide();

        static::assertFalse($this->manager->isShown());

        $this->manager->show();

        static::assertTrue($this->manager->isShown());
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Fake the sidebar configs.
     */
    public function fakeSidebarItemsConfig()
    {
        /** @var  \Illuminate\Contracts\Config\Repository  $config */
        $this->app['config']->set('testing.sidebar.menus', [
            'testing.sidebar.items.menu-1',
            'testing.sidebar.items.menu-2',
        ]);

        $this->app['config']->set('testing.sidebar.items', [
            'menu-1' => [
                'name'     => 'home',
                'title'    => 'HOME',
                'url'      => '/home',
                'icon'     => 'home-icon',
            ],
            'menu-2' => [
                'name'     => 'blog',
                'title'    => 'BLOG',
                'url'      => '/blog',
                'icon'     => 'blog-icon',
                'roles'    => ['blog-manager'],
                'children' => [
                    [
                        'name'        => 'blog-posts',
                        'title'       => 'POSTS',
                        'url'         => '/blog/posts',
                        'icon'        => 'posts-icon',
                        'roles'       => [],
                        'permissions' => ['blog.posts.crud']
                    ],
                    [
                        'name'     => 'blog-categories',
                        'title'    => 'CATEGORIES',
                        'url'      => '/blog/categories',
                        'icon'     => 'categories-icon',
                        'roles'    => [],
                        'permissions' => ['blog.categories.crud']
                    ],
                ],
            ],
        ]);
    }
}
