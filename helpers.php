<?php

if ( ! function_exists('sidebar')) {
    /**
     * Get the sidebar instance.
     *
     * @return \Arcanesoft\Sidebar\Contracts\Manager
     */
    function sidebar() {
        return app(Arcanesoft\Sidebar\Contracts\Manager::class);
    }
}
