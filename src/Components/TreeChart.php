<?php

namespace Tmc\LaravelTreeChart\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Tmc\LaravelTreeChart\Data\Node;

class TreeChart extends Component
{
    /** @var array<int, mixed> */
    public array $nodes;

    public array $options;

    public string $uid;

    public function __construct(mixed $nodes = [], array $options = [])
    {
        $this->options = array_merge(
            config('tree-chart', []),
            $options
        );

        $this->nodes = $this->normalizeCollection($nodes)->all();

        $this->uid = 'tc-'.uniqid();
    }

    protected function normalizeCollection(mixed $nodes): Collection
    {
        if ($nodes instanceof Collection) {
            return $nodes->map(fn ($node) => $this->normalizeNode($node, 0));
        }

        if (is_array($nodes)) {
            return collect($nodes)->map(fn ($node) => $this->normalizeNode($node, 0));
        }

        return collect([$nodes])->map(fn ($node) => $this->normalizeNode($node, 0));
    }

    protected function normalizeNode(mixed $node, int $depth): array
    {
        if ($node instanceof Node) {
            $node = $node->toArray();
        } elseif (is_object($node) && method_exists($node, 'toArray')) {
            $node = $node->toArray();
        } elseif (is_object($node)) {
            $node = (array) $node;
        }

        $node = is_array($node) ? $node : [];

        $children = $node['children'] ?? [];
        $children = $children instanceof Collection ? $children->all() : (array) $children;

        $normalized = [
            'id' => $node['id'] ?? 'n-'.($depth.'-'.uniqid()),
            'header' => (string) ($node['header'] ?? ''),
            'label' => (string) ($node['label'] ?? ''),
            'sub_label' => (string) ($node['sub_label'] ?? ''),
            'badge' => $node['badge'] ?? null,
            'badge_color' => $node['badge_color'] ?? null,
            'color' => $node['color'] ?? null,
            'width' => $node['width'] ?? null,
            'collapsed' => (bool) ($node['collapsed'] ?? false),
            'side_visible' => (bool) ($node['side_visible'] ?? true),
            'hideable' => (bool) ($node['hideable'] ?? false),
            'children' => collect($children)
                ->map(fn ($child) => $this->normalizeNode($child, $depth + 1))
                ->values()
                ->all(),
            'side' => $node['side'] ?? null,
            'depth' => $depth,
            'has_children' => count($children) > 0,
            'has_side' => ! empty($node['side'] ?? null),
        ];

        foreach ($node as $key => $value) {
            if (! array_key_exists($key, $normalized)) {
                $normalized['extra'][$key] = $value;
            }
        }

        return $normalized;
    }

    /**
     * Resolve effective color for a node (node override > per-level palette > fallback).
     */
    public function colorFor(array $node): string
    {
        if (! empty($node['color'])) {
            return $node['color'];
        }

        $colors = $this->options['colors'] ?? [];
        $depth = min($node['depth'] ?? 0, max(0, count($colors) - 1));

        return $colors[$depth] ?? '#6c757d';
    }

    public function widthFor(array $node): int
    {
        return (int) ($node['width'] ?? $this->options['card_width'] ?? 260);
    }

    public function render()
    {
        return view('tree-chart::components.tree-chart');
    }
}
