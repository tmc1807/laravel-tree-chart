<?php

use Tmc\LaravelTreeChart\Components\TreeChart;
use Tmc\LaravelTreeChart\Data\Node;

it('normalizes plain arrays recursively', function () {
    $component = new TreeChart([
        ['id' => 'a', 'label' => 'A', 'children' => [['id' => 'b', 'label' => 'B']]],
    ]);

    expect($component->nodes)
        ->toHaveCount(1)
        ->and($component->nodes[0]['id'])->toBe('a')
        ->and($component->nodes[0]['depth'])->toBe(0)
        ->and($component->nodes[0]['children'][0]['id'])->toBe('b')
        ->and($component->nodes[0]['children'][0]['depth'])->toBe(1)
        ->and($component->nodes[0]['children'][0]['has_children'])->toBeFalse();
});

it('normalizes collections and objects', function () {
    $node = Node::make('x', 'X')
        ->header('Header X')
        ->hideable()
        ->child(Node::make('y', 'Y')->collapsed());

    $component = new TreeChart(collect([$node]));

    expect($component->nodes[0]['header'])->toBe('Header X')
        ->and($component->nodes[0]['hideable'])->toBeTrue()
        ->and($component->nodes[0]['children'][0]['id'])->toBe('y')
        ->and($component->nodes[0]['children'][0]['collapsed'])->toBeTrue();
});

it('merges options over config defaults', function () {
    $component = new TreeChart([], ['card_width' => 333, 'animate' => false]);

    expect($component->options['card_width'])->toBe(333)
        ->and($component->options['animate'])->toBeFalse()
        ->and($component->options['side_width'])->toBe(500)
        ->and($component->options['colors'])->toBeArray();
});

it('resolves colors with node override winning over palette', function () {
    $component = new TreeChart([
        ['id' => 'a', 'label' => 'A', 'color' => '#ff00ff'],
    ], ['colors' => ['#111111']]);

    expect($component->colorFor($component->nodes[0]))->toBe('#ff00ff');

    $component2 = new TreeChart([
        ['id' => 'a', 'label' => 'A'],
    ], ['colors' => ['#111111']]);

    expect($component2->colorFor($component2->nodes[0]))->toBe('#111111');
});

it('keeps arbitrary extra node keys', function () {
    $component = new TreeChart([
        ['id' => 'a', 'label' => 'A', 'custom' => 'keep-me'],
    ]);

    expect($component->nodes[0]['extra']['custom'])->toBe('keep-me');
});
