<?php namespace Arcanesoft\Sidebar\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use JsonSerializable;

/**
 * Class     Item
 *
 * @package  Arcanesoft\Sidebar\Entitites
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Item implements Arrayable, Jsonable, JsonSerializable
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The item name.
     *
     * @var string
     */
    protected $name;

    /**
     * The item title.
     *
     * @var string
     */
    protected $title;

    /**
     * The item url.
     *
     * @var string
     */
    protected $url;

    /**
     * The item icon.
     *
     * @var string
     */
    protected $icon;

    /**
     * The item active state.
     *
     * @var bool
     */
    protected $active = false;

    /**
     * The item roles.
     *
     * @var array
     */
    protected $roles      = [];

    /**
     * The item permissions.
     *
     * @var array
     */
    protected $permissions = [];

    /**
     * The item children (sub-items).
     *
     * @var \Arcanesoft\Sidebar\Entities\ItemCollection
     */
    protected $children;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Item constructor.
     *
     * @param  string       $name
     * @param  string       $title
     * @param  string       $url
     * @param  string|null  $icon
     */
    public function __construct($name, $title, $url, $icon = null)
    {
        $this->name     = $name;
        $this->title    = $title;
        $this->url      = $url;
        $this->icon     = $icon;
        $this->active   = false;
        $this->children = new ItemCollection;
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the item name.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get the item title.
     *
     * @return string
     */
    public function title()
    {
        /** @var \Illuminate\Translation\Translator $trans */
        $trans = trans();

        return $trans->has($this->title) ? $trans->get($this->title) : $this->title;
    }

    /**
     * Set the title.
     *
     * @param  string  $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the item url.
     *
     * @return string
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * Get the item icon.
     *
     * @return string|null
     */
    public function icon()
    {
        return $this->icon;
    }

    /**
     * Set the item icon.
     *
     * @param  string  $icon
     *
     * @return self
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set the current name.
     *
     * @param  string  $name
     *
     * @return self
     */
    public function setCurrent($name)
    {
        $this->children->setCurrent($name);
        $this->active = ($this->name === $name || $this->children->hasActiveItem());

        return $this;
    }

    /**
     * Get the roles.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set the roles.
     *
     * @param  array  $roles
     *
     * @return self
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the permissions.
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set the permissions.
     *
     * @param  array  $permissions
     *
     * @return self
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Get the sub-items.
     *
     * @return \Arcanesoft\Sidebar\Entities\ItemCollection
     */
    public function children()
    {
        return $this->children;
    }

    /**
     * Get the active class.
     *
     * @param  string  $class
     *
     * @return string
     */
    public function activeClass($class = 'active')
    {
        return $this->isActive() ? $class : '';
    }

    /**
     * Get the sub-items class.
     *
     * @param  string  $class
     *
     * @return string
     */
    public function childrenClass($class = 'treeview')
    {
        return $this->hasChildren() ? $class : '';
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * Make the item.
     *
     * @param  string       $name
     * @param  string       $title
     * @param  string       $url
     * @param  string|null  $icon
     *
     * @return self
     */
    public static function make($name, $title, $url, $icon = null)
    {
        return new self($name, $title, $url, $icon);
    }

    /**
     * Make a Sidebar item from array.
     *
     * @param  array  $array
     *
     * @return self
     */
    public static function makeFromArray(array $array)
    {
        $item = self::make(
            $array['name'],
            $array['title'],
            self::getUrlFromArray($array),
            Arr::get($array, 'icon', null)
        );

        $item->setRoles(Arr::get($array, 'roles', []));
        $item->setPermissions(Arr::get($array, 'permissions', []));
        $item->addChildren(Arr::get($array, 'children', []));

        return $item;
    }

    /**
     * Get url from array.
     *
     * @param  array  $array
     *
     * @return string
     */
    private static function getUrlFromArray(array $array)
    {
        if (Arr::has($array, 'route'))
            return route(Arr::get($array, 'route'));

        return Arr::get($array, 'url', '#');
    }

    /**
     * Add children to the parent.
     *
     * @param  array  $children
     *
     * @return self
     */
    public function addChildren(array $children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }

        return $this;
    }

    /**
     * Add a sub-item to the parent.
     *
     * @param  array  $child
     *
     * @return self
     */
    public function addChild(array $child)
    {
        $item = self::makeFromArray($child);

        if ($item->allowed())
            $this->children->push($item);

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the item is active one.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Check if the item has children.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return ! $this->children->isEmpty();
    }

    /**
     * Check the user is allowed to see this item.
     *
     * @return bool
     */
    public function allowed()
    {
        /** @var  \Arcanesoft\Contracts\Auth\Models\User  $user */
        $user = auth()->user();

        if (is_null($user) || ( ! $this->hasRoles() && ! $this->hasPermissions()))
            return true;

        if ($user->isAdmin())
            return true;

        foreach ($this->roles as $roleSlug) {
            if ($user->hasRoleSlug($roleSlug))
                return true;
        }

        foreach ($this->permissions as $permissionSlug) {
            if ($user->may($permissionSlug))
                return true;
        }

        return $this->children()->first(function (Item $child) {
            return $child->allowed();
        }, false);
    }

    /**
     * Check if the item has roles.
     *
     * @return bool
     */
    public function hasRoles()
    {
        return ! empty($this->roles);
    }

    /**
     * Check if the item has permissions.
     *
     * @return bool
     */
    public function hasPermissions()
    {
        return ! empty($this->permissions);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name'        => $this->name(),
            'title'       => $this->title(),
            'url'         => $this->url(),
            'icon'        => $this->icon(),
            'active'      => $this->isActive(),
            'roles'       => $this->roles,
            'permissions' => $this->permissions,
            'children'    => $this->children->toArray(),
        ];
    }

    /**
     * Convert the object to its JSON representation.
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
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
