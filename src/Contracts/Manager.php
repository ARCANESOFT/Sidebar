<?php namespace Arcanesoft\Sidebar\Contracts;

/**
 * Interface  Manager
 *
 * @package   Arcanesoft\Sidebar\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface Manager
{
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
    public function setView($view);

    /**
     * Get the current item name.
     *
     * @return string
     */
    public function getCurrent();

    /**
     * Set the current item name.
     *
     * @param  string  $currentName
     *
     * @return $this
     */
    public function setCurrent($currentName);

    /**
     * Get the sidebar items.
     *
     * @return \Arcanesoft\Sidebar\Entities\ItemCollection
     */
    public function getItems();

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
    public function addRouteItem($name, $title, $route, array $parameters = [], $icon = null);

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
    public function addItem($name, $title, $url = '#', $icon = null);

    /**
     * Add an item from array.
     *
     * @param  array  $array
     *
     * @return $this
     */
    public function add(array $array);

    /**
     * Load items from multiple config keys.
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function loadItemsFromConfig($key);

    /**
     * Render the sidebar.
     *
     * @param  string|null  $view
     * @param  array        $data
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function render($view = null, array $data = []);

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the sidebar has items.
     *
     * @return bool
     */
    public function hasItems();
}
