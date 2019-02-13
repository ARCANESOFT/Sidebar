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
     * Set the selected item.
     *
     * @param  string  $name
     *
     * @return $this
     */
    public function setSelectedItem(string $name);

    /**
     * Get the sidebar items.
     *
     * @return \Arcanesoft\Sidebar\Collection
     */
    public function items();

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Add an item from array.
     *
     * @param  array  $array
     *
     * @return $this
     */
    public function add(array $array);

    /**
     * Load items from array.
     *
     * @param  array  $array
     *
     * @return mixed
     */
    public function loadFromArray(array $array);

    /**
     * Load items from multiple config keys.
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function loadFromConfig($key);

    /**
     * Show the sidebar.
     *
     * @return $this
     */
    public function show();

    /**
     * Hide the sidebar.
     *
     * @return $this
     */
    public function hide();

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

    /**
     * Check if the sidebar is shown.
     *
     * @return bool
     */
    public function isShown();
}
