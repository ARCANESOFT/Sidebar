<?php namespace Arcanesoft\Sidebar;

use Arcanesoft\Sidebar\Contracts\Manager as ManagerContract;
use Arcanesoft\Sidebar\Entities\Item;
use Arcanesoft\Sidebar\Entities\ItemCollection;
use Illuminate\Support\HtmlString;

/**
 * Class     Manager
 *
 * @package  Arcanesoft\Sidebar
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Manager implements ManagerContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /**
     * The view name.
     *
     * @var string
     */
    protected $view = '';

    /**
     * The current name.
     *
     * @var string
     */
    protected $currentName;

    /**
     * The sidebar items collection.
     *
     * @var \Arcanesoft\Sidebar\Entities\ItemCollection
     */
    protected $items;

    /**
     * The authenticated user.
     *
     * @var \Arcanesoft\Contracts\Auth\Models\User
     */
    protected $user;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->items = new ItemCollection;
        $this->setAuthenticatedUser();
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */
    /**
     * Set the view name.
     *
     * @param  string  $view
     *
     * @return self
     */
    public function setView($view)
    {
        if ( ! is_null($view))
            $this->view = $view;

        return $this;
    }

    /**
     * Get the current item name.
     *
     * @return string
     */
    public function getCurrent()
    {
        return $this->currentName;
    }

    /**
     * Set the current item name.
     *
     * @param  string  $currentName
     *
     * @return $this
     */
    public function setCurrent($currentName)
    {
        $this->currentName = $currentName;

        return $this;
    }

    /**
     * Get the sidebar items.
     *
     * @return \Arcanesoft\Sidebar\Entities\ItemCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * Add a routed item.
     *
     * @param  string       $name
     * @param  string       $title
     * @param  string       $route
     * @param  array        $parameters
     * @param  string|null  $icon
     *
     * @return self
     */
    public function addRouteItem($name, $title, $route, array $parameters = [], $icon = null)
    {
        return $this->addItem($name, $title, route($route, $parameters), $icon);
    }

    /**
     * Add an item.
     *
     * @param  string       $name
     * @param  string       $title
     * @param  string       $url
     * @param  string|null  $icon
     *
     * @return self
     */
    public function addItem($name, $title, $url = '#', $icon = null)
    {
        return $this->add(compact('name', 'title', 'url', 'icon'));
    }

    /**
     * Add an item from array.
     *
     * @param  array  $array
     *
     * @return self
     */
    public function add(array $array)
    {
        $item = Item::makeFromArray($array, $this->user);

        if ($item->allowed()) $this->items->push($item);

        return $this;
    }

    /**
     * Load items from multiple config keys.
     *
     * @param  string  $key
     *
     * @return self
     */
    public function loadItemsFromConfig($key)
    {
        foreach (config($key, []) as $configKey) {
            $this->loadItemFromConfig($configKey);
        }

        return $this;
    }

    /**
     * Load sidebar item from config file.
     *
     * @param  string  $key
     *
     * @return self
     */
    public function loadItemFromConfig($key)
    {
        if (config()->has($key)) {
            $this->add(config($key));
        }
        else {
            // Throw an exception ??
        }

        return $this;
    }

    /**
     * Render the sidebar.
     *
     * @param  string|null  $view
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function render($view = null)
    {
        $this->syncCurrentName()->setView($view);

        return new HtmlString(
            view($this->view, ['sidebarItems' => $this->getItems()])->render()
        );
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */
    /**
     * Check if the sidebar has items.
     *
     * @return bool
     */
    public function hasItems()
    {
        return ! $this->items->isEmpty();
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */
    /**
     * Sync the current name wih the sidebar items.
     *
     * @return self
     */
    private function syncCurrentName()
    {
        $this->items->setCurrent($this->currentName);

        return $this;
    }

    /**
     * Get the authenticated user.
     */
    private function setAuthenticatedUser()
    {
        if (auth()->guest()) return;

        $this->user = auth()->user()->load(['roles', 'roles.permissions']);
    }
}
