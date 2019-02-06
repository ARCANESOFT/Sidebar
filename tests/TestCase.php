<?php namespace Arcanesoft\Sidebar\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Class     TestCase
 *
 * @package  Arcanesoft\Sidebar\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class TestCase extends BaseTestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Arcanesoft\Sidebar\Tests\Stubs\TestServiceProvider::class,
            \Arcanesoft\Sidebar\SidebarServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = $app['config'];

        $config->set('view.paths', [
            resource_path('views'),
            realpath(__DIR__ .'/fixtures/resources/views'),
        ]);

        $this->setUpRoutes($app['router']);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * @param  \Illuminate\Routing\Router  $router
     */
    private function setUpRoutes($router)
    {
        $router->get('seo', function () { return 'SEO index page.'; })->name('seo.index');
    }
}
