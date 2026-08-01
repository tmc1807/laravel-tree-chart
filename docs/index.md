---
layout: default
title: Home
nav_order: 1
---

# laravel-tree-chart

**Framework-agnostic tree structure chart for Laravel Blade.**

A single, self-contained Blade component that renders any nested tree as an
interactive diagram — with colored cards per level, animated connector lines,
collapsible nodes, side panels, and the ability to hide branches. No Bootstrap,
no Livewire, no external JS/CSS: all styles and scripts are injected inline,
only once per page.

> Originally extracted from an RPJMD *pohon kinerja* (performance tree) module.
> Suitable for org charts, goal cascades, family trees, sitemaps, and more.

## Features

- **Data-driven** — pass any nested array/collection/objects; only `id` and `label` are required.
- **No UI dependencies** — own minimal CSS + vanilla JS, prefixed with `tc-` to avoid collisions.
- **Inline assets** — styles/scripts emitted once per page (`@once`); nothing to publish or build.
- **Collapsible nodes** — animated show/hide of children.
- **Optional photos** — circular avatar per node, with a placeholder when the node has no photo.
- **Side panels** — optional panel (any HTML/Blade) to the right of a card, toggleable via a switch.
- **Hideable nodes** — the `×` button hides a branch and leaves a clickable badge near the root.
- **Livewire-friendly** — auto-initializes trees added after page load, with no Livewire dependency.
- **Multiple instances** — several trees on one page share a single CSS/JS block.
- **Configurable** — per-level colors, card/side widths, animations, connector style.

## Quick preview

```php
$nodes = [
    [
        'id'     => 'visi',
        'header' => 'Visi',
        'label'  => 'Terwujudnya Masyarakat Sejahtera',
        'color'  => '#4e73df',
        'photo'  => 'https://example.test/foto/kepala-daerah.jpg', // opsional
        'children' => [
            ['id' => 'm-1', 'header' => 'Misi 1', 'label' => 'Meningkatkan kualitas SDM'],
        ],
    ],
];
```

Photo bersifat **opsional**: jika `photo` ada maka gambar tersebut yang ditampilkan
sebagai avatar bulat; jika tidak ada, placeholder dari opsi `photo_placeholder`
yang muncul (lihat [Node schema](node-schema.html) dan [Options](options.html)).

```blade
<x-tree-chart :nodes="$nodes" :options="['title' => 'Pohon Kinerja RPJMD 2025 - 2030']" />
```

## Documentation

- [Getting started](getting-started.html) — install & first tree.
- [Node schema](node-schema.html) — every node field explained.
- [Options](options.html) — configuration reference.
- [Advanced](advanced.html) — demo page, customization, Livewire, FAQ.

## License

MIT — see [LICENSE](https://github.com/tmc1807/laravel-tree-chart/blob/main/LICENSE).
