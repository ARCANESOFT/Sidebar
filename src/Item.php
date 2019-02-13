<?php namespace Arcanesoft\Sidebar;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use JsonSerializable;

/**
 * Class     Item
 *
 * @package  Arcanesoft\Sidebar
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Item implements Arrayable, Jsonable, JsonSerializable
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  string */
    protected $name;

    /** @var  string */
    public $title;

    /** @var  string */
    public $url;

    /** @var  string */
    public $icon;

    /** @var  \Arcanesoft\Sidebar\Collection */
    public $children;

    /** @var  array */
    protected $roles;

    /** @var  array */
    protected $permissions;

    /** @var boolean */
    private $selected;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * SidebarItem constructor.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes)
    {
        $this->name        = Arr::pull($attributes, 'name');
        $this->setTitle(Arr::pull($attributes, 'title'));
        $this->icon        = Arr::pull($attributes, 'icon');
        $this->roles       = Arr::pull($attributes, 'roles', []);
        $this->permissions = Arr::pull($attributes, 'permissions', []);
        $this->children    = (new Collection)->addItems(
            Arr::pull($attributes, 'children', [])
        );
        $this->selected    = false;

        $this->parseUrl($attributes);
    }

    /* -----------------------------------------------------------------
     |  Setters & Getters
     | -----------------------------------------------------------------
     */

    /**
     * Get the sidebar item's name.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Set the title.
     *
     * @param  string  $title
     *
     * @return $this
     */
    public function setTitle(string $title)
    {
        /** @var  \Illuminate\Translation\Translator  $translator */
        $translator  = trans();
        $this->title = $translator->has($title)
            ? $translator->get($title)
            : $title;

        return $this;
    }

    /**
     * Set the url.
     *
     * @param  string  $url
     *
     * @return $this
     */
    public function setUrl(string $url)
    {
        $this->url = url($url);

        return $this;
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
        $this->selected = ($this->name === $name);
        $this->children->setSelected($name);

        return $this;
    }

    /**
     * Get the roles.
     *
     * @return array|mixed
     */
    public function roles()
    {
        return $this->roles;
    }

    /**
     * Get the permissions.
     *
     * @return array|mixed
     */
    public function permissions()
    {
        return $this->permissions;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make a sidebar item.
     *
     * @param  array  $attributes
     *
     * @return \Arcanesoft\Sidebar\Item
     */
    public static function make(array $attributes = [])
    {
        return new static($attributes);
    }

    /**
     * Set the url from the route.
     *
     * @param  string  $name
     * @param  array   $params
     *
     * @return $this
     */
    public function route($name, array $params = [])
    {
        return $this->setUrl(
            route($name, $params)
        );
    }

    /**
     * Set the url from the action.
     *
     * @param  string|array  $name
     * @param  array         $params
     *
     * @return $this
     */
    public function action($name, array $params = [])
    {
        return $this->setUrl(
            action($name, $params)
        );
    }

    /**
     * Get the active/inactive class.
     *
     * @param  string  $active
     * @param  string  $inactive
     *
     * @return string
     */
    public function active($active = 'active', $inactive = '') : string
    {
        return $this->isActive() ? $active : $inactive;
    }

    /**
     * Push multiple child items.
     *
     * @param  array  $items
     *
     * @return $this
     */
    public function addChildren(array $items)
    {
        $this->children->addItems($items);

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if has children.
     *
     * @return bool
     */
    public function hasChildren() : bool
    {
        return $this->children->isNotEmpty();
    }

    /**
     * Check if has any selected children.
     *
     * @return bool
     */
    public function hasAnySelectedChildren() : bool
    {
        return $this->hasChildren()
            && $this->children->hasAnySelected();
    }

    /**
     * Check if the item is active.
     *
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->isSelected() || $this->hasAnySelectedChildren();
    }

    /**
     * Check if the item is selected.
     *
     * @return bool
     */
    public function isSelected() : bool
    {
        return $this->selected;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Parse the url attribute.
     *
     * @param  array  $attributes
     *
     * @return void
     */
    protected function parseUrl(array $attributes) : void
    {
        if (isset($attributes['url']))
            $this->setUrl($attributes['url']);

        if (isset($attributes['route']))
            $this->route(...Arr::wrap($attributes['route']));

        if (isset($attributes['action']))
            $this->action(...Arr::wrap($attributes['action']));
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name'        => $this->name,
            'title'       => $this->title,
            'url'         => $this->url,
            'icon'        => $this->icon,
            'active'      => $this->isActive(),
            'children'    => $this->children->toArray(),
            'roles'       => $this->roles,
            'permissions' => $this->permissions,
        ];
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param  int  $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Check if has roles.
     *
     * @return bool
     */
    public function hasRoles()
    {
        return ! empty($this->roles);
    }

    /**
     * Check if has permissions.
     *
     * @return bool
     */
    public function hasPermissions()
    {
        return ! empty($this->permissions);
    }
}
