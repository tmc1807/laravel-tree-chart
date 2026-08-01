<?php

namespace Tmc\LaravelTreeChart\Data;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Stringable;

/**
 * Fluent helper for building tree nodes.
 *
 * You do not have to use this class — plain arrays / objects / collections are
 * normalized automatically by the component. This builder just keeps the code
 * that constructs a tree readable.
 *
 * @method static static make(string $id, string $label)
 */
class Node
{
    public string $id;

    public string $header = '';

    public string $label;

    public string $subLabel = '';

    public ?string $badge = null;

    public ?string $badgeColor = null;

    public ?string $photo = null;

    public string $position = 'down';

    public ?string $color = null;

    public ?int $width = null;

    public bool $collapsed = false;

    public bool $sideVisible = true;

    public bool $hideable = false;

    /** @var array<int, mixed> */
    public array $children = [];

    public mixed $side = null;

    public array $extra = [];

    public function __construct(string $id, string $label)
    {
        $this->id = $id;
        $this->label = $label;
    }

    public static function make(string $id, string $label): static
    {
        return new static($id, $label);
    }

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function header(string $header): static
    {
        $this->header = $header;

        return $this;
    }

    public function subLabel(string $subLabel): static
    {
        $this->subLabel = $subLabel;

        return $this;
    }

    public function badge(?string $badge, ?string $color = null): static
    {
        $this->badge = $badge;
        $this->badgeColor = $color;

        return $this;
    }

    public function photo(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Where this node renders relative to its parent: 'side' (beside the
     * parent card) or 'down' (below the parent, the default).
     */
    public function position(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function width(?int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function collapsed(bool $collapsed = true): static
    {
        $this->collapsed = $collapsed;

        return $this;
    }

    public function sideVisible(bool $sideVisible = true): static
    {
        $this->sideVisible = $sideVisible;

        return $this;
    }

    public function hideable(bool $hideable = true): static
    {
        $this->hideable = $hideable;

        return $this;
    }

    /**
     * @param  array<int, mixed>|Collection<int, mixed>  $children
     */
    public function children(array|Collection $children): static
    {
        $this->children = $children instanceof Collection ? $children->all() : $children;

        return $this;
    }

    public function child(mixed $child): static
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * @param  string|Htmlable|Renderable|Stringable|Closure|null  $side
     */
    public function side(mixed $side): static
    {
        $this->side = $side;

        return $this;
    }

    public function extra(array $extra): static
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Convert to a plain array consumable by the component.
     */
    public function toArray(): array
    {
        $node = [
            'id' => $this->id,
            'header' => $this->header,
            'label' => $this->label,
            'sub_label' => $this->subLabel,
            'badge' => $this->badge,
            'badge_color' => $this->badgeColor,
            'photo' => $this->photo,
            'position' => $this->position,
            'color' => $this->color,
            'width' => $this->width,
            'collapsed' => $this->collapsed,
            'side_visible' => $this->sideVisible,
            'hideable' => $this->hideable,
            'children' => array_map(
                fn ($child) => $child instanceof self ? $child->toArray() : $child,
                $this->children
            ),
            'side' => $this->side,
        ];

        return array_merge($node, $this->extra);
    }
}
