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

it('starts everything collapsed with expand_level click', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
            'children' => [['id' => 'b', 'label' => 'Beta']],
        ]],
        'options' => ['expand_level' => 'click'],
    ])->render();

    expect($html)
        ->toContain('Alpha')
        ->not->toContain('tc-collapse tc-open');
});

it('auto-expands only up to the configured level', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
            'children' => [[
                'id' => 'b',
                'label' => 'Beta',
                'children' => [['id' => 'c', 'label' => 'Gamma']],
            ]],
        ]],
        'options' => ['expand_level' => 1],
    ])->render();

    expect(substr_count($html, 'tc-collapse tc-open'))
        ->toBe(1) // level 0 expanded
        ->and(substr_count($html, 'class="tc-collapse'))
        ->toBe(2) // level 0 + level 1, only level 0 open
        ->and($html)->toContain('Beta');
});

it('lets an explicit collapsed flag override expand_level', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
            'collapsed' => true,
            'children' => [['id' => 'b', 'label' => 'Beta']],
        ]],
        'options' => ['expand_level' => 'all'],
    ])->render();

    expect($html)
        ->not->toContain('tc-collapse tc-open');
});

it('renders side-positioned children beside the parent card', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
            'children' => [
                ['id' => 's1', 'label' => 'Samping', 'position' => 'side'],
                ['id' => 'd1', 'label' => 'Bawah', 'position' => 'down'],
            ],
        ]],
        'options' => [],
    ])->render();

    expect($html)
        ->toContain('tc-anchor-row')
        ->toContain('tc-side-node')
        ->toContain('Samping')
        ->toContain('Bawah');
});

it('keeps side-positioned children out of the down-children row', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
            'children' => [
                ['id' => 's1', 'label' => 'Samping', 'position' => 'side'],
                ['id' => 'd1', 'label' => 'Bawah'],
            ],
        ]],
        'options' => [],
    ])->render();

    $anchorRowPos = strpos($html, 'class="tc-anchor-row"');
    $collapsePos = strpos($html, 'class="tc-collapse');
    $sideLabelPos = strpos($html, 'Samping');
    $downLabelPos = strpos($html, 'Bawah');

    expect($sideLabelPos)->not->toBeFalse()
        ->and($downLabelPos)->not->toBeFalse()
        ->and($anchorRowPos)->not->toBeFalse()
        ->and($collapsePos)->not->toBeFalse()
        ->and($sideLabelPos)->toBeLessThan($collapsePos)
        ->and($downLabelPos)->toBeGreaterThan($collapsePos);
});

it('hides side-positioned children while the parent is collapsed', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
            'collapsed' => true,
            'children' => [
                ['id' => 's1', 'label' => 'Samping', 'position' => 'side'],
                ['id' => 'd1', 'label' => 'Bawah'],
            ],
        ]],
        'options' => [],
    ])->render();

    preg_match('/class="tc-node[^"]*"\s+data-tc-id="a"/', $html, $m);

    expect($m)->not->toBeEmpty()
        ->and($m[0])->not->toContain('tc-open')
        ->and($html)->toContain('tc-side-node')
        ->and($html)->toContain('.tc-node:not(.tc-open) > .tc-anchor-row > .tc-side-node { display: none; }');
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

it('renders the sticky bottom scrollbar by default', function () {
    $html = view('tree', [
        'nodes' => [['id' => 'a', 'label' => 'Alpha']],
        'options' => [],
    ])->render();

    expect($html)
        ->toContain('class="tc-scrollbar" data-tc-scrollbar')
        ->toContain('data-tc-scrollbar-thumb')
        ->toContain('.tc-scrollbar {')
        ->toContain('TreeChart.updateScrollbar');
});

it('omits the sticky scrollbar when disabled', function () {
    $html = view('tree', [
        'nodes' => [['id' => 'a', 'label' => 'Alpha']],
        'options' => ['sticky_scrollbar' => false],
    ])->render();

    expect($html)->not->toContain('class="tc-scrollbar" data-tc-scrollbar');
});

it('tags the wrapper as dashed when connector is dashed', function () {
    $html = view('tree', [
        'nodes' => [['id' => 'a', 'label' => 'Alpha']],
        'options' => ['connector' => 'dashed'],
    ])->render();

    expect($html)->toContain('tc-tree-chart tc-animate tc-connector-dashed');
});

it('keeps side connectors dashed in dashed mode and solid in solid mode', function () {
    $html = view('tree', [
        'nodes' => [['id' => 'a', 'label' => 'Alpha']],
        'options' => ['connector' => 'solid'],
    ])->render();

    expect($html)
        ->not->toContain('tc-tree-chart tc-animate tc-connector-dashed')
        ->toContain('.tc-tree-chart:not(.tc-connector-dashed) .tc-side-node-connector {')
        ->toContain('.tc-tree-chart:not(.tc-connector-dashed) .tc-side-connector {');
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

it('hides the photo area when the photo feature is disabled, even with data', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
            'photo' => 'https://example.test/avatar.jpg',
        ]],
        'options' => ['photo' => false],
    ])->render();

    expect($html)
        ->not->toContain('class="tc-photo"')
        ->not->toContain('src="https://example.test/avatar.jpg"');
});

it('renders a photo area when enabled, with or without data', function () {
    $html = view('tree', [
        'nodes' => [[
            'id' => 'a',
            'label' => 'Alpha',
        ]],
        'options' => ['photo' => true, 'photo_placeholder' => 'https://example.test/placeholder.png'],
    ])->render();

    expect($html)
        ->toContain('class="tc-photo"')
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
