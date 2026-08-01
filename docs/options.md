---
layout: default
title: Options
parent: Home
nav_order: 4
---

# Options

Pass options per component, or set global defaults in `config/tree-chart.php`
(publish with `php artisan vendor:publish --tag=tree-chart-config`).
Per-component options always win.

```blade
<x-tree-chart :nodes="$nodes" :options="[
    'title'    => 'Cascading Bagan Kinerja RPJMD 2025 - 2030',
    'subtitle' => 'Periode akhir RPJMD',
    'colors'   => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#6f42c1'],
]" />
```

## Reference

| Option | Type | Default | Description |
| --- | --- | --- | --- |
| `title` | string \| null | `null` | Title rendered above the tree. |
| `subtitle` | string \| null | `null` | Muted subtitle below the title. |
| `colors` | array | `['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#6f42c1']` | Palette applied per depth level (index 0 = root). A node's own `color` overrides it. |
| `card_width` | int | `260` | Default card width in px. |
| `photo_placeholder` | string \| null | *inline SVG data-URI* | Fallback image shown when a node has no `photo`. Set to `null` to hide the avatar area entirely for photo-less nodes. |
| `side_width` | int | `500` | Default side-panel width in px. |
| `animate` | bool | `true` | Staggered enter/exit animations for cards and connector lines. |
| `connector` | string | `dashed` | Connector line style: `dashed` or `solid`. |
| `collapsible` | bool | `true` | Render a chevron that toggles children. |
| `side_toggle` | bool | `true` | Render a switch to show/hide side panels. |
| `scrollable` | bool | `true` | Wrap the tree in a horizontally scrollable container. |
| `demo` | bool | `false` | Register the `/tree-chart/demo` route (development only). |

## Colors

Each depth level picks its color from `colors`. A node that defines `color`
uses its own value instead:

```php
$options = ['colors' => ['#4e73df', '#1cc88a', '#36b9cc']];
// depth 0 (root)  -> #4e73df
// depth 1         -> #1cc88a
// depth 2         -> #36b9cc
```

## Config file

The published config:

```php
<?php

return [
    'colors'      => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#6f42c1'],
    'card_width'  => 260,
    'photo_placeholder' => 'data:image/svg+xml,...',
    'side_width'  => 500,
    'animate'     => true,
    'connector'   => 'dashed',
    'collapsible' => true,
    'side_toggle' => true,
    'scrollable'  => true,
    'title'       => null,
    'subtitle'    => null,
    'demo'        => false,
];
```
