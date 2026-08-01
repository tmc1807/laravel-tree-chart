---
layout: default
title: Node schema
parent: Home
nav_order: 3
---

# Node schema

A node is any nested array (or object / `Collection` — everything is normalized
automatically). Only `id` and `label` are required.

| Key | Type | Description |
| --- | --- | --- |
| `id` | string | Unique id. Used for DOM ids, collapse targets, hide/restore. |
| `header` | string | Text in the colored header bar (e.g. `Misi 1`). Omit to hide the header bar. |
| `label` | string | Main text in the card body. |
| `sub_label` | string | Secondary muted text below the label. |
| `badge` | string | Small pill in the body (e.g. `3 Tujuan`). |
| `badge_color` | string | Pill color. Defaults to the node color. |
| `color` | string | Hex color for card border, header background and connector lines. Falls back to the per-level palette. |
| `width` | int | Card width in px. Defaults to the `card_width` option. |
| `children` | array | Nested child nodes (recursive). |
| `side` | string \| Htmlable | Side-panel content rendered as-is (`{!! !!}`). Pass `view('name', [...])` or raw HTML. |
| `side_visible` | bool | Whether the side panel starts visible. Default `true`. |
| `collapsed` | bool | Start with children collapsed. Default `false`. |
| `hideable` | bool | Show a `×` button that hides the branch into a badge. Default `false`. |

Any extra keys are preserved in `extra` and ignored by the renderer.

## Example

```php
$nodes = [
    [
        'id'          => 'root',
        'header'      => 'Root',
        'label'       => 'Main objective',
        'sub_label'   => 'Periode 2025 - 2030',
        'badge'       => '3 anak',
        'badge_color' => '#1cc88a',
        'color'       => '#4e73df',
        'width'       => 320,
        'collapsed'   => false,
        'hideable'    => true,
        'side'        => view('partials.detail', ['data' => $detail]),
        'children'    => [/* ... */],
    ],
];
```

## Side panels

The `side` field is rendered with `{!! !!}`, so you can pass any renderable:

- `view('partials.indicator', [...])` — a Blade partial (e.g. an indicator table).
- Raw HTML string.
- Anything implementing `Htmlable` / `Stringable`.

```php
'side' => view('partials.indicator', [
    'title' => 'Indikator Tujuan',
    'rows'  => [
        ['IPM', '%', '70.1', '71.2', '72.5'],
        ['RLS', 'tahun', '8.5', '8.8', '9.1'],
    ],
]),
```

The panel appears to the right of the card, connected by a dashed line. If the
`side_toggle` option is enabled, a switch in the card header shows/hides it.

## Builder helper

Prefer a fluent API? Use the `Node` builder instead of arrays:

```php
use Tmc\LaravelTreeChart\Data\Node;

$nodes = [
    Node::make('visi', 'Terwujudnya Masyarakat Sejahtera')
        ->header('Visi')
        ->color('#4e73df')
        ->child(
            Node::make('m-1', 'Meningkatkan kualitas SDM')
                ->header('Misi 1')
                ->badge('2 Tujuan', '#1cc88a')
                ->hideable()
                ->child(Node::make('t-1', 'Meningkatkan kualitas pendidikan')),
        ),
];
```

Available methods: `id()`, `header()`, `label()`, `subLabel()`, `badge()`,
`color()`, `width()`, `collapsed()`, `sideVisible()`, `hideable()`,
`children()`, `child()`, `side()`, `extra()`.
