<?php namespace Arcanesoft\Sidebar;

use Arcanesoft\Sidebar\Contracts\Manager as ManagerContract;
use Illuminate\Support\Collection as IlluminateCollection;

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

    /** @var  \Arcanesoft\Sidebar\Collection */
    protected $items;

    /** @var  bool */
    protected $shown = true;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    public function __construct()
    {
        $this->items = new Collection;
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set the selected item.
     *
     * @param  string  $name
     *
     * @return $this
     */
    public function setSelectedItem(string $name)
    {
        $this->items->setSelected($name);

        return $this;
    }

    /**
     * Get the sidebar items.
     *
     * @return \Arcanesoft\Sidebar\Collection
     */
    public function items()
    {
        return $this->items;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Load the sidebar items from config files.
     *
     * @param  array  $items
     *
     * @return $this
     */
    public function loadFromArray(array $items)
    {
        foreach ($items as $item) {
            if (is_array($item) && ! empty($item)) {
                $this->add($item);
            }
        }

        return $this;
    }

    /**
     * Load sidebar items from config file(s).
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function loadFromConfig($key)
    {
        $items = new IlluminateCollection(config()->get($key, []));

        if ($items->isEmpty()) return $this;

        $allHasConfig = $items->every(function ($item) {
            return config()->has($item);
        });

        if ($allHasConfig) {
            $items->each(function ($item) {
                $this->loadFromConfig($item);
            });

            return $this;
        }

        return $this->add($items->toArray());
    }

    /**
     * Add an item from array.
     *
     * @param  array  $item
     *
     * @return $this
     */
    public function add(array $item)
    {
        $this->items->addItem($item);

        return $this;
    }

    /**
     * Show the sidebar.
     *
     * @return $this
     */
    public function show()
    {
        $this->shown = true;

        return $this;
    }

    /**
     * Hide the sidebar.
     *
     * @return $this
     */
    public function hide()
    {
        $this->shown = false;

        return $this;
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
        return $this->items->isNotEmpty();
    }

    /**
     * Check if the sidebar is shown.
     *
     * @return bool
     */
    public function isShown()
    {
        return $this->shown === true;
    }
}
