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
}
