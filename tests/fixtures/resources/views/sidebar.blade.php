<?php
/**
 * Render sidebar item.
 *
 * @param  \Arcanesoft\Sidebar\Entities\Item  $item
 *
 * @return string
 */
function renderSidebarItem($item)
{
    $output   = [];
    $output[] = '<li class="'.trim($item->childrenClass().' '.$item->activeClass('active open')).'">';

    if ($item->hasChildren()) {
        $output[] = '<a href="javascript:void(0);">';
        $output[] = '<i class="'.$item->icon().'"></i> <span>'.$item->title().'</span> <i class="fa fa-angle-left pull-right"></i>';
        $output[] = '</a>';
        $output[] = '<ul class="treeview-menu">';
        $output[] = $item->children()->transform(function ($child) { return renderSidebarItem($child); })->implode(PHP_EOL);
        $output[] = '</ul>';
    }
    else {
        $output[] = '<a href="'.$item->url().'">';
        $output[] = '<i class="'.$item->icon().'"></i> <span>'.$item->title().'</span>';
        $output[] = '</a>';
    }

    $output[] = '</li>';

    return implode(PHP_EOL, $output);
}
?>

<ul class="sidebar-menu">
    <li class="header">{{ trans('foundation::navigation.sidebar.main-navigation') }}</li>
    @foreach($sidebarItems as $item)
        {!! renderSidebarItem($item) !!}
    @endforeach
</ul>
