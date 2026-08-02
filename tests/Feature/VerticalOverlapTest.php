<?php

it('vertical overlap: parent with tall table pushes children down', function () {
    $nodes = [
        [
            'id' => 'root',
            'label' => 'Root',
            'children' => [
                [
                    'id' => 'parent',
                    'label' => 'Parent with Table',
                    'body' => '<table style="width:100%"><tr><td>Row 1</td></tr><tr><td>Row 2</td></tr><tr><td>Row 3</td></tr><tr><td>Row 4</td></tr><tr><td>Row 5</td></tr><tr><td>Row 6</td></tr><tr><td>Row 7</td></tr><tr><td>Row 8</td></tr><tr><td>Row 9</td></tr><tr><td>Row 10</td></tr></table>',
                    'children' => [
                        ['id' => 'child1', 'label' => 'Child 1'],
                        ['id' => 'child2', 'label' => 'Child 2'],
                        ['id' => 'child3', 'label' => 'Child 3'],
                    ],
                ],
            ],
        ],
    ];

    $html = view('tree', [
        'nodes' => $nodes,
        'options' => ['animate' => false],
    ])->render();

    expect($html)
        ->toContain('tc-tree-chart')
        ->toContain('Root')
        ->toContain('Parent with Table')
        ->toContain('Child 1')
        ->toContain('Child 2')
        ->toContain('Child 3');
});
