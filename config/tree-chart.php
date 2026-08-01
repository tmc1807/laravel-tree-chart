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
    | Placeholder photo (shown when a node has no `photo`)
    |--------------------------------------------------------------------------
    |
    | URL or data-URI used as the fallback image for nodes without a photo.
    | Set to null to hide the photo area entirely when a node has no photo.
    |
    */

    'photo_placeholder' => 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 80 80%22%3E%3Crect width=%2280%22 height=%2280%22 fill=%22%23e9ecef%22/%3E%3Ccircle cx=%2240%22 cy=%2232%22 r=%2214%22 fill=%22%23adb5bd%22/%3E%3Cpath d=%22M14 74c0-14 12-22 26-22s26 8 26 22z%22 fill=%22%23adb5bd%22/%3E%3C/svg%3E',

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
