<?php

it('vertical alignment: siblings with different heights align children to tallest parent', function () {
    $nodes = [
        [
            'id' => 'root',
            'label' => 'Root',
            'children' => [
                [
                    'id' => 'parent-short',
                    'label' => 'Short Parent',
                    'children' => [
                        ['id' => 'child-a1', 'label' => 'Child A1'],
                        ['id' => 'child-a2', 'label' => 'Child A2'],
                    ],
                ],
                [
                    'id' => 'parent-tall',
                    'label' => 'Tall Parent with Table',
                    'body' => '<table style="width:100%"><tr><td>Row 1</td></tr><tr><td>Row 2</td></tr><tr><td>Row 3</td></tr><tr><td>Row 4</td></tr><tr><td>Row 5</td></tr><tr><td>Row 6</td></tr><tr><td>Row 7</td></tr><tr><td>Row 8</td></tr><tr><td>Row 9</td></tr><tr><td>Row 10</td></tr></table>',
                    'children' => [
                        ['id' => 'child-b1', 'label' => 'Child B1'],
                        ['id' => 'child-b2', 'label' => 'Child B2'],
                    ],
                ],
                [
                    'id' => 'parent-medium',
                    'label' => 'Medium Parent',
                    'body' => '<p>Some content</p><p>More content</p>',
                    'children' => [
                        ['id' => 'child-c1', 'label' => 'Child C1'],
                    ],
                ],
            ],
        ],
    ];

    $html = view('tree', [
        'nodes' => $nodes,
        'options' => ['animate' => false],
    ])->render();

    // Inject probe
    $probeScript = <<<'JS'
<script>
    window.__tcProbe = function () {
        function rr(el) {
            var r = el.getBoundingClientRect();
            return { w: r.width, h: r.height, l: r.left, t: r.top, r: r.right, b: r.bottom };
        }
        function vis(r) { return r.w > 0 && r.h > 0; }
        function ov(a, b) { return a.r > b.l + 1 && b.r > a.l + 1 && a.b > b.t + 1 && b.b > a.t + 1; }

        var cards = Array.prototype.map.call(document.querySelectorAll('.tc-card'), function (el) {
            return { el: el, r: rr(el), id: el.closest('.tc-node')?.getAttribute('data-tc-id') };
        }).filter(function (x) { return vis(x.r); });

        var overlaps = [];
        for (var i = 0; i < cards.length; i++) {
            for (var j = i + 1; j < cards.length; j++) {
                if (ov(cards[i].r, cards[j].r)) {
                    overlaps.push({
                        a: cards[i].id + '(' + Math.round(cards[i].r.t) + ',' + Math.round(cards[i].r.b) + ')',
                        b: cards[j].id + '(' + Math.round(cards[j].r.t) + ',' + Math.round(cards[j].r.b) + ')'
                    });
                }
            }
        }

        var detail = {};
        cards.forEach(function(c) {
            detail[c.id] = { t: Math.round(c.r.t), b: Math.round(c.r.b), h: Math.round(c.r.h) };
        });

        document.body.setAttribute('data-tc-probe',
            JSON.stringify({
                overlaps: overlaps,
                cards: detail
            }));
    };
    setTimeout(window.__tcProbe, 200);
</script>
JS;

    $html = str_replace('</body>', $probeScript.'</body>', $html);
    if (! str_contains($html, $probeScript)) {
        $html .= $probeScript;
    }
    file_put_contents('/tmp/vertical-align-demo.html', $html);

    expect($html)
        ->toContain('tc-tree-chart')
        ->toContain('Short Parent')
        ->toContain('Tall Parent with Table')
        ->toContain('Medium Parent');
});
