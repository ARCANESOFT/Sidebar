<?php namespace Arcanesoft\Sidebar\Entities;

use Arcanesoft\Contracts\Auth\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

/**
 * Class     Item
 *
 * @package  Arcanesoft\Sidebar\Entitites
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Item extends Fluent
{
    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Item constructor.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $keys = ['name', 'title', 'url', 'icon'];

        parent::__construct(Arr::only($attributes, $keys) + [
            'extra' => Arr::except($attributes, $keys)
        ]);

        $this->attributes['active']   = false;
        $this->attributes['children'] = new ItemCollection;
        $this->setRoles([])->setPermissions([]);
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
        return $this->get('name');
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

        return $trans->has($title = $this->get('title')) ? $trans->get($title) : $title;
    }

    /**
     * Get the item url.
     *
     * @return string
     */
    public function url()
    {
        return $this->get('url');
    }

    /**
     * Get the item icon.
     *
     * @return string|null
     */
    public function icon()
    {
        return $this->get('icon');
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
        $this->attributes['children']->setCurrent($name);
        $this->attributes['active'] = ($this->name() === $name || $this->children()->hasActiveItem());

        return $this;
    }

    /**
     * Get the roles.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->get('roles', []);
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
        $this->attributes['roles'] = $roles;

        return $this;
    }

    /**
     * Get the permissions.
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->get('permissions', []);
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
        $this->attributes['permissions'] = $permissions;

        return $this;
    }

    /**
     * Get the sub-items.
     *
     * @return \Arcanesoft\Sidebar\Entities\ItemCollection
     */
    public function children()
    {
        return $this->get('children', new ItemCollection);
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
        return new self(compact('name', 'title', 'url', 'icon'));
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
        return tap(
            self::make($array['name'], $array['title'], self::getUrlFromArray($array), Arr::get($array, 'icon', null)),
            function (Item $item) use ($array) {
                $item->setRoles(Arr::get($array, 'roles', []));
                $item->setPermissions(Arr::get($array, 'permissions', []));
                $item->addChildren(Arr::get($array, 'children', []));
            }
        );
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
        return Arr::has($array, 'route')
            ? route(Arr::get($array, 'route'))
            : Arr::get($array, 'url', '#');
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
            $this->attributes['children']->push($item);

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
        return (bool) $this->get('active', false);
    }

    /**
     * Check if the item has children.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return ! $this->children()->isEmpty();
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

        return $user->isAdmin()
            || $this->checkRoles($user)
            || $this->checkPermission($user)
            || $this->hasAllowedChild();
    }

    /**
     * Check if the item has roles.
     *
     * @return bool
     */
    public function hasRoles()
    {
        return ! empty($this->getRoles());
    }

    /**
     * Check if the item has permissions.
     *
     * @return bool
     */
    public function hasPermissions()
    {
        return ! empty($this->getPermissions());
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Convert the instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($value) {
            return $value instanceof Arrayable ? $value->toArray() : $value;
        }, $this->attributes);
    }

    /**
     * Check if the user has role.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     *
     * @return bool
     */
    private function checkRoles(User $user)
    {
        foreach ($this->getRoles() as $roleSlug) {
            if ($user->hasRoleSlug($roleSlug)) return true;
        }

        return false;
    }

    /**
     * Check if the user has permission.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     *
     * @return bool
     */
    private function checkPermission(User $user)
    {
        foreach ($this->getPermissions() as $permissionSlug) {
            if ($user->may($permissionSlug)) return true;
        }

        return false;
    }

    /**
     * Check if has an allowed child.
     *
     * @return bool
     */
    private function hasAllowedChild()
    {
        return $this->children()->filter(function (Item $child) {
            return $child->allowed();
        })->isNotEmpty();
    }
}
