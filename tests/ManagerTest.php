<?php namespace Arcanesoft\Sidebar\Tests;

use Symfony\Component\DomCrawler\Crawler;

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
            $this->assertInstanceOf($expected, $this->manager);
        }

        $this->assertFalse($this->manager->hasItems());
        $this->assertCount(0, $this->manager->getItems());
    }

    /** @test */
    public function it_can_add_one_item()
    {
        $this->manager->addItem(
            $name = 'auth',
            $title = 'Authorization',
            $url = 'http://localhot/dashboard/auth',
            $icon = 'fa fa-fw fa-users'
        );

        $this->assertTrue($this->manager->hasItems());

        $items = $this->manager->getItems();

        $this->assertCount(1, $items);

        /** @var  \Arcanesoft\Sidebar\Entities\Item  $item */
        $item = $items->first();

        $this->assertSame($title, $item->title());
        $this->assertSame($icon, $item->icon());
        $this->assertEmpty($item->getRoles());
        $this->assertEmpty($item->getPermissions());
    }

    /** @test */
    public function it_can_set_and_get_current_name()
    {
        $this->assertNull($this->manager->getCurrent());

        $this->manager->setCurrent('seo-dashboard');

        $this->assertSame('seo-dashboard', $this->manager->getCurrent());
    }

    /** @test */
    public function it_can_add_item_with_route()
    {
        $this->manager->addRouteItem('seo', 'SEO', 'seo.index', [], 'seo-icon');

        $expected = [
            [
                'name'        => 'seo',
                'title'       => 'SEO',
                'url'         => 'http://localhost/seo',
                'icon'        => 'seo-icon',
                'active'      => false,
                'roles'       => [],
                'permissions' => [],
                'children'    => [],
            ],
        ];

        $this->assertEquals($expected, $this->manager->getItems()->toArray());
    }

    /** @test */
    public function it_can_render()
    {
        $this->manager->addItem('home', 'HOME', '/', 'home-icon');
        $this->manager->add([
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
            ]
        ]);

        $rendered = $this->manager->render('sidebar');

        $this->assertInstanceOf(\Illuminate\Support\HtmlString::class, $rendered);

        $crawler = new Crawler($rendered->toHtml());

        $this->assertCount(4, $crawler->filter('a'));
        $this->assertCount(1, $crawler->filter('a[href="javascript:void(0);"]')); // BLOG link
    }

    /**
     * @test
     *
     * @expectedException         \InvalidArgumentException
     * @expectedExceptionMessage  View [_includes.sidebar.default] not found.
     */
    public function it_must_throw_an_error_if_view_not_found()
    {
        $this->manager->render();
    }
}
