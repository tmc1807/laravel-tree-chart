<?php

it('renders root nodes and their labels', function () {
    $html = view('tree', [
        'nodes' => [['id' => 'a', 'label' => 'Alpha']],
        'options' => [],
    ])->render();

    expect($html)
        ->toContain('tc-tree-chart')
        ->toContain('Alpha');
});

it('renders nested children recursively', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'root',
            'label' => 'Root',
            'children' => [[
                'id' => 'child',
                'label' => 'Child',
                'children' => [['id' => 'grand', 'label' => 'Grand']],
            ]],
        ]],
        'options' => [],
    ])->render();

    expect($html)
        ->toContain('Root')
        ->toContain('Child')
        ->toContain('Grand')
        ->toContain('tc-collapse tc-open');
});

it('renders side panel content when provided', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
            'side' => '<strong>PANEL</strong>',
        ]],
        'options' => [],
    ])->render();

    expect($html)
        ->toContain('tc-side show')
        ->toContain('<strong>PANEL</strong>');
});

it('renders close button for hideable nodes and header text', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'header' => 'Misi 1',
            'label' => 'Alpha',
            'hideable' => true,
        ]],
        'options' => [],
    ])->render();

    expect($html)
        ->toContain('Misi 1')
        ->toContain('data-tc-hide');
});

it('honours collapsed nodes', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
            'collapsed' => true,
            'children' => [['id' => 'b', 'label' => 'Beta']],
        ]],
        'options' => [],
    ])->render();

    expect($html)
        ->toContain('Alpha')
        ->not->toContain('tc-collapse tc-open');
});

it('injects styles and scripts only once per page', function () {
    $html = view('two-trees', [
        'nodesA' => [['id' => 'a', 'label' => 'A']],
        'nodesB' => [['id' => 'b', 'label' => 'B']],
    ])->render();

    expect(substr_count($html, 'tc-tree-chart {'))
        ->toBe(1)
        ->and(substr_count($html, 'window.TreeChart'))
        ->toBe(1);
});

it('uses configured colors per level when node color is absent', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
            'children' => [['id' => 'b', 'label' => 'Beta']],
        ]],
        'options' => ['colors' => ['#111111', '#222222']],
    ])->render();

    expect($html)
        ->toContain('--tc-node-color:#111111')
        ->toContain('--tc-node-color:#222222')
        ->toContain('--tc-children-color:#111111');
});

it('renders the node photo when provided', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
            'photo' => 'https://example.test/avatar.jpg',
        ]],
        'options' => [],
    ])->render();

    expect($html)
        ->toContain('tc-photo')
        ->toContain('src="https://example.test/avatar.jpg"');
});

it('falls back to the configured placeholder when node has no photo', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
        ]],
        'options' => ['photo_placeholder' => 'https://example.test/placeholder.png'],
    ])->render();

    expect($html)
        ->toContain('tc-photo')
        ->toContain('src="https://example.test/placeholder.png"');
});

it('hides the photo area when node has no photo and no placeholder is set', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
        ]],
        'options' => ['photo_placeholder' => null],
    ])->render();

    expect($html)
        ->not->toContain('class="tc-photo"');
});
