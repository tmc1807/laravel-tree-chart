<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>body{margin:0;padding:20px;} *{transition:none!important;animation:none!important;}</style>
</head>
<body>
<x-tree-chart :nodes="$nodes" :options="$options ?? []" />

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
</body>
</html>