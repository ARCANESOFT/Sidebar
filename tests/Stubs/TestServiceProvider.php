<?php namespace Arcanesoft\Sidebar\Tests\Stubs;

use Arcanedev\Support\ServiceProvider;
use Illuminate\Routing\Router;

/**
 * Class     TestServiceProvider
 *
 * @package  Arcanesoft\Sidebar\Tests\Stubs
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class TestServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    public function boot()
    {
        parent::boot();

        $this->loadTranslationsFrom(__DIR__.'/../fixtures/resources/lang', 'sidebar');

        $this->registerRoutes($this->app['router']);
    }

    /* -----------------------------------------------------------------
     |  Other Functions
     | -----------------------------------------------------------------
     */
    /**
     * Register the routes for tests.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    private function registerRoutes(Router $router)
    {
        $router->group(['as' => 'sidebar::seo.'], function (Router $router) {
            $router->get('/', function() {
                return 'SEO stats';
            })->name('stats'); // sidebar::seo.stats

            $router->get('pages', function() {
                return 'SEO pages';
            })->name('pages'); // sidebar::seo.pages
        });
    }
}
