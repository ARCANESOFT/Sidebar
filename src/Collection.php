<?php namespace Arcanesoft\Sidebar;

use Illuminate\Support\Collection as BaseCollection;

/**
 * Class     Collection
 *
 * @package  Arcanesoft\Sidebar
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Collection extends BaseCollection
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Add a sidebar item.
     *
     * @param  \Arcanesoft\Sidebar\Item  $item
     *
     * @return $this
     */
    public function add(Item $item)
    {
        return $this->push($item);
    }

    /**
     * Push multiple sidebar items into the collection.
     *
     * @param  array  $items
     *
     * @return $this
     */
    public function addItems(array $items)
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }

        return $this;
    }

    /**
     * Push a new sidebar item to the collection.
     *
     * @param  array  $attributes
     *
     * @return $this
     */
    public function addItem(array $attributes)
    {
        return $this->add(new Item($attributes));
    }

    /**
     * Set the selected item.
     *
     * @param  string  $name
     *
     * @return $this
     */
    public function setSelected(string $name)
    {
        return $this->transform(function (Item $item) use ($name) {
            return $item->setSelected($name);
        });
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if there is any item selected.
     *
     * @return bool
     */
    public function hasAnySelected() : bool
    {
        return $this->filter(function (Item $item) {
            return $item->isActive();
        })->isNotEmpty();
    }
}
