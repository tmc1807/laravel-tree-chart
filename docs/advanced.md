---
layout: default
title: Advanced
parent: Home
nav_order: 5
---

# Advanced

## Demo page

A sample page ships with the package. Enable it only in development:

```bash
# .env
TREECHART_DEMO=true
```

Then point the config at the env value (if you published it):

```php
'demo' => env('TREECHART_DEMO', false),
```

Visit `/tree-chart/demo`. When `demo` is `false` (the default) the route is not
registered.

## Styling

All CSS is scoped under `.tc-tree-chart` with a `tc-` prefix, so it will not
collide with your app styles. Override it with plain CSS after the component
renders — for example:

```css
.tc-tree-chart .tc-card { border-radius: 6px; }
.tc-tree-chart .tc-head { text-transform: none; }
```

Key class names:

| Class | Element |
| --- | --- |
| `.tc-tree-chart` | Root wrapper (also carries `tc-animate`, `tc-connector-dashed`). |
| `.tc-tree`, `.tc-tree-scroll` | Tree layout + horizontal scroll. |
| `.tc-node`, `.tc-up` | Node column and the vertical connector line. |
| `.tc-tree-children`, `.tc-hline` | Sibling row and horizontal connector. |
| `.tc-anchor`, `.tc-card` | Card wrapper and card. |
| `.tc-head`, `.tc-body`, `.tc-title`, `.tc-sub`, `.tc-badge` | Card parts. |
| `.tc-side`, `.tc-side-card`, `.tc-side-connector` | Side panel. |
| `.tc-collapse` (+ `.tc-open`) | Collapsible children container. |
| `.tc-switch` | Pure-CSS toggle switch. |
| `.tc-hidden-badges`, `.tc-hidden-badge` | Hide/restore badges near the root. |

## JavaScript

A single `window.TreeChart` object is injected once per page:

- `TreeChart.init(root?)` — initialize any not-yet-initialized charts in `root`.
- `TreeChart.updateHlines(root?)` — recalculate connector line positions.
- `TreeChart.stagger(root?)` — replay the staggered entrance animation.
- `TreeChart.toggleCollapse(node)` — expand/collapse a node's children.
- `TreeChart.toggleSide(input)` — toggle a side panel.
- `TreeChart.hideNode(domId)` — hide a branch into a badge.

The script auto-initializes on `DOMContentLoaded` and watches the DOM with a
`MutationObserver`, so trees added later (including via Livewire morphs) are
picked up automatically.

## Livewire

No integration code is required. The component renders plain HTML; the observer
re-initializes charts whenever new nodes appear in the DOM. If you re-render a
tree and want the connector lines to re-align, call:

```js
TreeChart.updateHlines();
```

## Hiding a branch programmatically

Every hideable node renders a `×` button. To hide a branch from your own code,
call the same helper the button uses:

```js
TreeChart.hideNode('tc-<uid>-<node-id>');
```

The `<uid>` and sanitized `<node-id>` make up the node's `data-tc-dom`.

## FAQ

**Can I render several trees on one page?**
Yes. Multiple `<x-tree-chart>` components share a single CSS/JS block (`@once`)
and each tree initializes independently.

**Do I need Bootstrap or Livewire?**
No. The package is self-contained; both are optional.

**Can I use Eloquent models directly?**
Yes — any object with a `toArray()` method (models, `Data` objects) is
normalized automatically. Only `id`/`label` need to exist.

**Why do connector lines disappear on some screenshots?**
Horizontal lines are measured at runtime and only drawn for rows that actually
render. If a row is fully collapsed, its lines are intentionally hidden.

## Contributing

See [Contributing](contributing.html).
