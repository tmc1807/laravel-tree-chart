<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default colors per tree level (depth 0 = root)
    |--------------------------------------------------------------------------
    |
    | Used to color card borders, header backgrounds and connector lines when a
    | node does not define its own `color`.
    |
    */

    'colors' => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#6f42c1'],

    /*
    |--------------------------------------------------------------------------
    | Default card width (px)
    |--------------------------------------------------------------------------
    */

    'card_width' => 260,

    /*
    |--------------------------------------------------------------------------
    | Default side panel width (px)
    |--------------------------------------------------------------------------
    |
    | Nodes with a `side` panel will reserve this much horizontal space to the
    | right of the card when the panel is visible.
    |
    */

    'side_width' => 500,

    /*
    |--------------------------------------------------------------------------
    | Animations
    |--------------------------------------------------------------------------
    |
    | Enable staggered enter/exit animations for cards and connector lines.
    |
    */

    'animate' => true,

    /*
    |--------------------------------------------------------------------------
    | Connector line style
    |--------------------------------------------------------------------------
    |
    | "solid" or "dashed".
    |
    */

    'connector' => 'dashed',

    /*
    |--------------------------------------------------------------------------
    | Collapsible nodes
    |--------------------------------------------------------------------------
    |
    | When true, nodes render a chevron that toggles their children.
    |
    */

    'collapsible' => true,

    /*
    |--------------------------------------------------------------------------
    | Side panel toggle
    |--------------------------------------------------------------------------
    |
    | When true, nodes with a `side` panel render a switch to show/hide it.
    |
    */

    'side_toggle' => true,

    /*
    |--------------------------------------------------------------------------
    | Optional title / subtitle rendered above the tree
    |--------------------------------------------------------------------------
    */

    'title' => null,

    'subtitle' => null,

    /*
    |--------------------------------------------------------------------------
    | Scrollable tree
    |--------------------------------------------------------------------------
    |
    | Wrap the tree in a horizontally scrollable container.
    |
    */

    'scrollable' => true,

    /*
    |--------------------------------------------------------------------------
    | Demo page
    |--------------------------------------------------------------------------
    |
    | When true, registers a GET /tree-chart/demo route rendering a sample tree.
    | Intended for development only — keep false in production.
    |
    */

    'demo' => false,

];
