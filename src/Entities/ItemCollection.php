<?php namespace Arcanesoft\Sidebar\Entities;

use Illuminate\Support\Collection;

/**
 * Class     ItemCollection
 *
 * @package  Arcanesoft\Core\Helpers\Sidebar\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ItemCollection extends Collection
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */
    /**
     * Set the current name to the items collection.
     *
     * @param  string  $currentName
     *
     * @return self
     */
    public function setCurrent($currentName)
    {
        return $this->transform(function (Item $item) use ($currentName) {
            return $item->setCurrent($currentName);
        });
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */
    /**
     * Check if the items collection has an active one.
     *
     * @return bool
     */
    public function hasActiveItem()
    {
        return ! $this->filter(function (Item $item) {
            return $item->isActive();
        })->isEmpty();
    }
}
