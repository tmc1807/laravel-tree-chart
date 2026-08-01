---
layout: default
title: Getting started
parent: Home
nav_order: 2
---

# Getting started

## Requirements

- PHP 8.2+
- Laravel 11 / 12

## Installation

```bash
composer require tmc/laravel-tree-chart
```

Laravel auto-discovers the service provider. If your app has package discovery
disabled, register it manually in `bootstrap/providers.php`:

```php
Tmc\LaravelTreeChart\TreeChartServiceProvider::class,
```

### Local development (path repository)

To develop or test against a local checkout of the package:

```json
{
    "repositories": [
        { "type": "path", "url": "../laravel-tree-chart" }
    ]
}
```

```bash
composer require tmc/laravel-tree-chart:@dev
```

## Your first tree

Create the node data (for example in a controller):

```php
$nodes = [
    [
        'id'       => 'visi',
        'header'   => 'Visi',
        'label'    => 'Terwujudnya Masyarakat yang Adil dan Sejahtera',
        'color'    => '#4e73df',
        'children' => [
            [
                'id'       => 'm-1',
                'header'   => 'Misi 1',
                'label'    => 'Meningkatkan kualitas sumber daya manusia',
                'badge'    => '2 Tujuan',
                'hideable' => true,
                'children' => [
                    [
                        'id'    => 't-1',
                        'label' => 'Meningkatkan kualitas pendidikan',
                        'side'  => view('partials.indicator', ['rows' => $indikatorRows]),
                    ],
                ],
            ],
        ],
    ],
];
```

Render it in any Blade view:

```blade
<x-tree-chart :nodes="$nodes" :options="$options ?? []" />
```

That's it. The component injects its own CSS and JavaScript — nothing to
publish, nothing to include in your layout.

## Publishing the config (optional)

To tweak the global defaults:

```bash
php artisan vendor:publish --tag=tree-chart-config
```

The published file lives at `config/tree-chart.php`. Per-component `:options`
always take precedence over the config.

## Next steps

- Learn about every [node field](node-schema.html).
- Browse the [options reference](options.html).
- See the [demo page](advanced.html) and [customization](advanced.html).
