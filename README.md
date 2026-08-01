# laravel-tree-chart

[![PHP](https://img.shields.io/badge/PHP-8.2+-8892BF?logo=php&logoColor=fff)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11%20%7C%2012-red?logo=laravel&logoColor=fff)](https://laravel.com)
[![Tests](https://github.com/tmc1807/laravel-tree-chart/actions/workflows/tests.yml/badge.svg)](https://github.com/tmc1807/laravel-tree-chart/actions)
[![Docs](https://img.shields.io/badge/docs-github%20pages-4B7BEC?logo=github)](https://tmc1807.github.io/laravel-tree-chart)
[![License: MIT](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Framework-agnostic tree structure chart for Laravel Blade.

A single, self-contained Blade component that renders any nested tree as an interactive diagram — with colored cards per level, animated connector lines, collapsible nodes, side panels, and the ability to hide nodes. No Bootstrap, no Livewire, no external JS/CSS: all styles and scripts are injected inline, only once per page.

> Originally extracted from an RPJMD *pohon kinerja* (performance tree) module — suitable for org charts, goal cascades, family trees, sitemaps, and more.

## Documentation

Full documentation is available at **<https://tmc1807.github.io/laravel-tree-chart>**:

- [Getting started](https://tmc1807.github.io/laravel-tree-chart/getting-started.html)
- [Node schema](https://tmc1807.github.io/laravel-tree-chart/node-schema.html)
- [Options](https://tmc1807.github.io/laravel-tree-chart/options.html)
- [Advanced](https://tmc1807.github.io/laravel-tree-chart/advanced.html)
- [Contributing](https://tmc1807.github.io/laravel-tree-chart/contributing.html)

## Features

- **Data-driven** — pass any nested array/collection/objects; no schema requirements beyond `id` + `label`.
- **No UI dependencies** — own minimal CSS + vanilla JS, prefixed with `tc-` to avoid collisions.
- **Inline assets** — styles/scripts are emitted once per page (`@once`), nothing to publish or build.
- **Collapsible nodes** — animated show/hide of children.
- **Side panels** — an optional panel (any HTML/Blade) to the right of a card, toggleable via a switch.
- **Hideable nodes** — the `×` button hides a branch and leaves a clickable badge near the root.
- **Livewire-friendly** — auto-initializes trees added after page load (mutation observer), no Livewire dependency.
- **Multiple instances** — several trees on one page share a single CSS/JS block.
- **Configurable** — per-level colors, card/side widths, animations, connector style.

## Installation

```bash
composer require tmc1807/laravel-tree-chart
```

For local development via a path repository:

```json
{
    "repositories": [
        { "type": "path", "url": "../laravel-tree-chart" }
    ]
}
```

Laravel auto-discovers the service provider. If your app has package discovery disabled, register it manually in `bootstrap/providers.php`:

```php
Tmc\LaravelTreeChart\TreeChartServiceProvider::class,
```

## Quick start

```blade
<x-tree-chart :nodes="$nodes" :options="$options" />
```

Build the nodes as nested arrays:

```php
$nodes = [
    [
        'id'       => 'visi',
        'header'   => 'Visi',
        'label'    => 'Terwujudnya Masyarakat Sejahtera',
        'color'    => '#4e73df',
        'children' => [
            [
                'id'       => 'm-1',
                'header'   => 'Misi 1',
                'label'    => 'Meningkatkan kualitas SDM',
                'badge'    => '2 Tujuan',
                'hideable' => true,
                'children' => [
                    [
                        'id'    => 't-1',
                        'label' => 'Meningkatkan kualitas pendidikan',
                        'side'  => view('partials.indicator', ['rows' => $indikatorRows]),
                        'children' => [
                            [
                                'id'    => 's-1',
                                'label' => 'Meningkatnya mutu layanan pendidikan',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
```

## Node schema

| Key | Type | Description |
| --- | --- | --- |
| `id` | string | Unique id (used for DOM ids, collapse, hide/restore). |
| `header` | string | Text in the colored header bar (e.g. `Misi 1`). Omit to hide the header bar. |
| `label` | string | Main text in the card body. |
| `sub_label` | string | Secondary muted text in the card body. |
| `badge` | string | Small pill shown in the body (e.g. `3 Tujuan`). |
| `badge_color` | string | Pill color; defaults to the node color. |
| `photo` | string | Optional image URL rendered as a circular avatar. If empty, the `photo_placeholder` option is shown instead. |
| `position` | string | Placement relative to parent: `down` (below, default) or `side` (beside the parent card). |
| `color` | string | Hex color for card border, header, and lines. Falls back to the level palette. |
| `width` | int | Card width in px; defaults to `card_width` option. |
| `children` | array | Nested nodes. |
| `side` | string \| Htmlable | Side panel content, rendered as-is (`{!! !!}`). Pass `view('name', [...])` or HTML. |
| `side_visible` | bool | Whether the side panel starts visible (default `true`). |
| `collapsed` | bool | Start with children collapsed (default `false`). |
| `hideable` | bool | Show a `×` button that hides the branch into a badge (default `false`). |

Any additional keys are kept in `extra` and ignored by the renderer.

### Builder helper

Instead of arrays you can use the fluent `Node` builder:

```php
use Tmc\LaravelTreeChart\Data\Node;

$nodes = [
    Node::make('visi', 'Terwujudnya Masyarakat Sejahtera')
        ->header('Visi')
        ->color('#4e73df')
        ->photo('https://example.test/foto/visi.jpg')
        ->child(
            Node::make('m-1', 'Meningkatkan kualitas SDM')
                ->header('Misi 1')
                ->badge('2 Tujuan', '#1cc88a')
                ->hideable()
        ),
];
```

## Options

All options are optional; defaults live in `config/tree-chart.php` (publish with `php artisan vendor:publish --tag=tree-chart-config`).

```php
$options = [
    'title'       => 'Cascading Bagan Kinerja RPJMD 2025 - 2030',
    'subtitle'    => 'Akhir periode',
    'colors'      => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#6f42c1'], // per level (depth 0 = root)
    'card_width'  => 260,
    'photo'       => true, // master switch: false hides the avatar even when a node provides a photo
    'photo_placeholder' => 'data:image/svg+xml,...', // fallback when a node has no photo; null hides the avatar
    'side_width'  => 500,
    'animate'     => true,
    'connector'   => 'dashed', // 'dashed' | 'solid'
    'collapsible' => true,
    'expand_level' => 'all', // 'all' (semua tampil) | 'click' (klik baru muncul) | 3 (auto sampai level 3)
    'side_toggle' => true,
    'scrollable'  => true,
];
```

## Demo

A sample page is included. Enable it only in development:

```bash
# .env
TREECHART_DEMO=true
```

```php
// config/tree-chart.php (published) or dynamically
'tree-chart.demo' => env('TREECHART_DEMO', false),
```

Then visit `/tree-chart/demo`. When disabled (default), the route is not registered.

## Testing & style

```bash
composer install
composer test   # Pest (testbench)
composer pint   # Laravel Pint
```

## License

MIT
