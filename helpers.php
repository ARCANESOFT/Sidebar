<?php

use Arcanesoft\Sidebar\Contracts\Manager;

if ( ! function_exists('sidebar')) {
    /**
     * Get the sidebar instance.
     *
     * @return \Arcanesoft\Sidebar\Contracts\Manager
     */
    function sidebar() {
        return app(Manager::class);
    }
}
