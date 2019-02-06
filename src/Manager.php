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
    protected $view;

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
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function add(array $array)
    {
        $item = Item::makeFromArray($array);

        if ($item->allowed())
            $this->items->push($item);

        return $this;
    }

    /**
     * Load items from multiple config keys.
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function loadItemsFromConfig($key)
    {
        foreach (config($key, []) as $key) {
            if (config()->has($key)) {
                $this->add(config($key));
            }
            else {
                // Throw an exception ??
            }
        }

        return $this;
    }

    /**
     * Render the sidebar.
     *
     * @param  string|null  $view
     * @param  array        $data
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function render($view = null, array $data = [])
    {
        $this->syncCurrentName()->setView($view ?: '_includes.sidebar.default');

        return new HtmlString(
            view($this->view, array_merge($data, ['sidebarItems' => $this->getItems()]))->render()
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
     * @return $this
     */
    private function syncCurrentName()
    {
        $this->items->setCurrent($this->currentName);

        return $this;
    }
}
