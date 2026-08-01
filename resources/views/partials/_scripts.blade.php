@once('tree-chart-scripts')
<script>
window.TreeChart = (function () {
    var SIDE_HIDING = 320;
    var COLLAPSE_HIDING = 300;
    var HIDE_NODE_HIDING = 400;

    function closest(el, sel) { return el.closest ? el.closest(sel) : null; }

    function stagger(root) {
        var scope = root || document;
        var rootEl = scope === document ? document.documentElement : scope;
        var els = scope.querySelectorAll('.tc-card, .tc-up, .tc-hline, .tc-tree-children');
        var i, el;

        for (i = 0; i < els.length; i++) {
            el = els[i];
            el.classList.remove('tc-hiding');
            el.style.animation = 'none';
            el.style.setProperty('--tc-delay', Math.min(i * 60, 1200) + 'ms');
        }
        void rootEl.offsetHeight;
        for (i = 0; i < els.length; i++) {
            el = els[i];
            el.style.animation = '';
            el.style.setProperty('--tc-delay', Math.min(i * 60, 1200) + 'ms');
        }
    }

    function hideCollapse(node, done) {
        var collapse = node.querySelector(':scope > .tc-collapse');
        if (!collapse) return done();
        collapse.querySelectorAll('.tc-card, .tc-up, .tc-hline, .tc-tree-children').forEach(function (el, i) {
            el.classList.add('tc-hiding');
            el.style.setProperty('--tc-delay', Math.min(i * 40, 400) + 'ms');
        });
        setTimeout(function () {
            collapse.classList.remove('tc-open');
            node.classList.remove('tc-open');
            collapse.querySelectorAll('.tc-hiding').forEach(function (el) { el.classList.remove('tc-hiding'); });
            done();
        }, COLLAPSE_HIDING);
    }

    return {
        /** Re-trigger the entrance stagger animation inside the given root. */
        stagger: stagger,

        /** (Re)initialize every chart inside the given root. Idempotent. */
        init: function (root) {
            var scope = root || document;
            var charts = scope.querySelectorAll('.tc-tree-chart:not([data-tc-inited])');
            charts.forEach(function (chart) {
                chart.setAttribute('data-tc-inited', '1');
                TreeChart.initInstance(chart);
            });
            TreeChart.attachCloseButtons(scope);
            TreeChart.updateHlines(scope);
        },

        initInstance: function (chart) {
            chart.querySelectorAll('.tc-side.show').forEach(function (side) {
                var node = closest(side, '.tc-node');
                if (node) node.style.marginRight = (chart.getAttribute('data-tc-side-width') || 500) + 'px';
            });

            chart.addEventListener('click', function (e) {
                var caret = closest(e.target, '[data-tc-collapse]');
                if (caret) {
                    e.stopPropagation();
                    TreeChart.toggleCollapse(closest(caret, '.tc-node'));
                    return;
                }
                var hideBtn = closest(e.target, '[data-tc-hide]');
                if (hideBtn) {
                    e.stopPropagation();
                    TreeChart.hideNode(hideBtn.getAttribute('data-tc-hide'));
                    return;
                }
                var card = closest(e.target, '.tc-card');
                if (!card) return;
                if (closest(e.target, '.tc-side-card')) return;
                if (closest(e.target, '.tc-switch')) return;
                TreeChart.toggleCollapse(closest(card, '.tc-node'));
            });
        },

        layoutSideNodes: function (root) {
            var scope = root || document;
            scope.querySelectorAll('.tc-anchor-row').forEach(function (row) {
                var sides = [];
                var i;
                for (i = 0; i < row.children.length; i++) {
                    if (row.children[i].classList.contains('tc-side-node')) sides.push(row.children[i]);
                }
                if (!sides.length) return;

                var node = closest(row, '.tc-node');
                var collapse = node ? node.querySelector(':scope > .tc-collapse') : null;
                var children = (collapse && collapse.classList.contains('tc-open'))
                    ? collapse.querySelector(':scope > .tc-tree-children') : null;

                var rowRect = row.getBoundingClientRect();
                var cardRect = row.querySelector(':scope > .tc-anchor .tc-card').getBoundingClientRect();
                var cardRight = rowRect.left + rowRect.width;
                var cardMid = cardRect.top + cardRect.height / 2;

                var gap = 14;
                var chart = closest(row, '.tc-tree-chart');
                var gapRef = chart ? chart.querySelector('.tc-tree-children') : null;
                if (gapRef) {
                    var g = parseFloat(getComputedStyle(gapRef).gap);
                    if (g > 0) gap = g;
                }

                var reserve = 0;
                sides.forEach(function (side) {
                    var nodeW = side.offsetWidth;
                    if (nodeW <= 0) return;
                    var overhang = Math.max(0, (nodeW - cardRect.width) / 2);

                    var sideTop = rowRect.top;
                    var sideBottom = rowRect.top + side.offsetHeight;

                    var requiredRight = cardRight;
                    if (children) {
                        var downCards = children.querySelectorAll('.tc-card');
                        var i2;
                        for (i2 = 0; i2 < downCards.length; i2++) {
                            var d = downCards[i2].getBoundingClientRect();
                            if (d.width <= 0 || d.height <= 0) continue;
                            if (d.bottom <= sideTop + 1 || d.top >= sideBottom - 1) continue;
                            if (d.right > requiredRight) requiredRight = d.right;
                        }
                    }

                    var cardLeft = requiredRight > cardRight
                        ? requiredRight + gap + overhang
                        : cardRight + gap;
                    var sideLeft = cardLeft - overhang;

                    side.style.left = Math.round(sideLeft - rowRect.left) + 'px';

                    var connector = side.querySelector(':scope > .tc-side-node-connector');
                    if (connector) {
                        connector.style.left = Math.round(cardRight - sideLeft) + 'px';
                        connector.style.width = Math.max(gap, Math.round(cardLeft - cardRight)) + 'px';
                        connector.style.top = Math.round(cardMid - rowRect.top - 1) + 'px';
                    }

                    reserve = Math.max(reserve, sideLeft + nodeW - cardRight);
                });

                if (node) {
                    if (reserve > 0) {
                        node.style.marginRight = Math.round(reserve) + 'px';
                    } else {
                        node.style.marginRight = '';
                    }
                }
            });
        },

        layoutDownNodes: function (root) {
            var scope = root || document;
            scope.querySelectorAll('.tc-tree-children').forEach(function (container) {
                var nodes = Array.from(container.children).filter(function (el) {
                    return el.classList.contains('tc-node') && el.style.display !== 'none';
                });
                if (nodes.length < 2) return;

                function ownCard(n) {
                    var c = n.querySelector(':scope > .tc-anchor-row > .tc-anchor > .tc-card');
                    return c ? c.getBoundingClientRect() : null;
                }

                function childRect(n) {
                    var cards = n.querySelectorAll(':scope > .tc-collapse .tc-card');
                    var r = null;
                    for (var k = 0; k < cards.length; k++) {
                        var b = cards[k].getBoundingClientRect();
                        if (b.width <= 0 || b.height <= 0) continue;
                        if (!r) { r = { l: b.left, t: b.top, r: b.right, b: b.bottom }; continue; }
                        r.l = Math.min(r.l, b.left); r.t = Math.min(r.t, b.top);
                        r.r = Math.max(r.r, b.right); r.b = Math.max(r.b, b.bottom);
                    }
                    return r;
                }

                function overlap(a, b) {
                    return a && b && a.r > b.l + 4 && b.r > a.l + 4 && a.b > b.t + 4 && b.b > a.t + 4;
                }

                function pin() {
                    nodes.forEach(function (n) {
                        var c = ownCard(n);
                        var w = c ? Math.round(c.width) : 0;
                        n.style.width = w > 0 ? w + 'px' : '';
                    });
                }

                var i, j, guard = 0, changed = true;
                pin();
                while (changed && guard++ < 24) {
                    changed = false;
                    for (i = 0; i < nodes.length; i++) {
                        var ci = childRect(nodes[i]);
                        var oi = ownCard(nodes[i]);
                        for (j = i + 1; j < nodes.length; j++) {
                            var cj = childRect(nodes[j]);
                            var oj = ownCard(nodes[j]);
                            if (overlap(ci, oj) || overlap(oi, cj) || overlap(ci, cj)) {
                                if (nodes[i].style.width) { nodes[i].style.width = ''; changed = true; }
                                if (nodes[j].style.width) { nodes[j].style.width = ''; changed = true; }
                            }
                        }
                    }
                }
            });
        },

        fitScroll: function (root) {
            var scope = root || document;
            scope.querySelectorAll('.tc-tree-chart').forEach(function (chart) {
                var scroll = chart.querySelector('.tc-tree-scroll');
                var tree = chart.querySelector('.tc-tree');
                if (!scroll || !tree) return;

                tree.style.paddingLeft = '0';
                tree.style.paddingRight = '0';
                void tree.offsetHeight;

                function cardBounds() {
                    var minL = null, maxR = null;
                    chart.querySelectorAll('.tc-card').forEach(function (c) {
                        var r = c.getBoundingClientRect();
                        if (r.width <= 0 || r.height <= 0) return;
                        if (minL === null || r.left < minL) minL = r.left;
                        if (maxR === null || r.right > maxR) maxR = r.right;
                    });
                    return { minL: minL, maxR: maxR };
                }

                var b = cardBounds();
                if (b.minL === null) return;

                var treeRect = tree.getBoundingClientRect();
                var padL = Math.max(0, Math.round(treeRect.left - b.minL));
                tree.style.paddingLeft = padL + 'px';
                void tree.offsetHeight;

                treeRect = tree.getBoundingClientRect();
                b = cardBounds();
                var padR = Math.max(0, Math.round(b.maxR - treeRect.right));
                tree.style.paddingRight = padR + 'px';
            });
        },

        updateHlines: function (root) {
            var scope = root || document;
            TreeChart.layoutDownNodes(scope);
            TreeChart.layoutSideNodes(scope);
            scope.querySelectorAll('.tc-tree-children').forEach(function (container) {
                var hline = container.querySelector(':scope > .tc-hline');
                if (!hline) return;

                var nodes = Array.from(container.children).filter(function (el) {
                    return el.classList.contains('tc-node') && el.style.display !== 'none';
                });

                function hide() { hline.style.display = 'none'; hline.style.width = '0'; }
                if (nodes.length === 0) { hide(); return; }

                void container.offsetHeight;
                var containerRect = container.getBoundingClientRect();
                var hasSide = nodes.some(function (n) {
                    return n.style.marginRight && parseFloat(n.style.marginRight) > 0;
                });

                var firstCenter, lastCenter;
                if (nodes.length === 1 && hasSide) {
                    var childUp = nodes[0].querySelector('.tc-up');
                    if (!childUp) { hide(); return; }
                    var childRect = childUp.getBoundingClientRect();
                    firstCenter = containerRect.width / 2;
                    lastCenter = childRect.left + childRect.width / 2 - containerRect.left;
                } else if (nodes.length < 2) {
                    hide(); return;
                } else {
                    var firstUp = nodes[0].querySelector('.tc-up');
                    var lastUp = nodes[nodes.length - 1].querySelector('.tc-up');
                    if (!firstUp || !lastUp) { hide(); return; }
                    var firstRect = firstUp.getBoundingClientRect();
                    var lastRect = lastUp.getBoundingClientRect();
                    firstCenter = firstRect.left + firstRect.width / 2 - containerRect.left;
                    lastCenter = lastRect.left + lastRect.width / 2 - containerRect.left;
                }

                var left = Math.min(firstCenter, lastCenter);
                var right = Math.max(firstCenter, lastCenter);
                hline.style.display = 'block';
                hline.style.left = left + 'px';
                hline.style.width = Math.max(0, right - left) + 'px';
                hline.style.right = 'auto';
            });

            TreeChart.fitScroll(scope);
        },

        toggleCollapse: function (node) {
            if (!node) return;
            var collapse = node.querySelector(':scope > .tc-collapse');
            if (!collapse) return;

            if (collapse.classList.contains('tc-open')) {
                hideCollapse(node, function () { setTimeout(TreeChart.updateHlines, 30); });
            } else {
                collapse.classList.add('tc-open');
                node.classList.add('tc-open');
                stagger(collapse);
                setTimeout(function () { TreeChart.updateHlines(); setTimeout(TreeChart.updateHlines, 30); }, 30);
            }
        },

        toggleSide: function (input) {
            if (!input) return;
            var side = document.getElementById(input.getAttribute('data-tc-side'));
            if (!side) return;
            var node = closest(side, '.tc-node');
            var chart = closest(side, '.tc-tree-chart');
            var width = parseInt(chart ? chart.getAttribute('data-tc-side-width') : '500', 10) || 500;

            if (input.checked) {
                side.classList.add('show');
                if (node) node.style.marginRight = width + 'px';
                stagger(side);
                setTimeout(function () { TreeChart.updateHlines(); setTimeout(TreeChart.updateHlines, 30); }, 20);
            } else {
                side.querySelectorAll('.tc-card, .tc-side-connector').forEach(function (el, i) {
                    el.classList.add('tc-hiding');
                    el.style.setProperty('--tc-delay', Math.min(i * 50, 350) + 'ms');
                });
                setTimeout(function () {
                    if (input.checked) return;
                    side.classList.remove('show');
                    if (node) node.style.marginRight = '';
                    side.querySelectorAll('.tc-hiding').forEach(function (el) { el.classList.remove('tc-hiding'); });
                    setTimeout(function () { TreeChart.updateHlines(); setTimeout(TreeChart.updateHlines, 30); }, 20);
                }, SIDE_HIDING);
            }
        },

        hideNode: function (domId) {
            var node = document.querySelector('[data-tc-dom="' + domId + '"]');
            if (!node || node.style.display === 'none') return;

            var chart = closest(node, '.tc-tree-chart');
            var label = node.getAttribute('data-tc-label') || node.getAttribute('data-tc-id') || domId;

            var badges = chart ? chart.querySelector('[data-tc-hidden-badges]') : null;
            if (badges) {
                var badge = document.createElement('span');
                badge.className = 'tc-hidden-badge';
                badge.innerHTML = '<span>' + label + '</span><span class="tc-x">\u00d7</span>';
                (function (nodeRef, badgeEl) {
                    badgeEl.addEventListener('click', function () {
                        nodeRef.style.display = '';
                        badgeEl.remove();
                        stagger(chart || nodeRef);
                        TreeChart.updateHlines();
                    });
                })(node, badge);
                badges.appendChild(badge);
            }

            node.querySelectorAll('.tc-card, .tc-up, .tc-hline, .tc-tree-children').forEach(function (el, i) {
                el.classList.add('tc-hiding');
                el.style.setProperty('--tc-delay', Math.min(i * 40, 400) + 'ms');
            });

            setTimeout(function () {
                if (node.style.display === 'none') return;
                node.style.display = 'none';
                node.querySelectorAll('.tc-hiding').forEach(function (el) { el.classList.remove('tc-hiding'); });
                TreeChart.updateHlines();
            }, HIDE_NODE_HIDING);
        },

        attachCloseButtons: function (root) {
            (root || document).querySelectorAll('[data-tc-hide]').forEach(function (btn) {
                if (btn.dataset.tcBound) return;
                btn.dataset.tcBound = '1';
            });
        }
    };
})();

(function () {
    if (window.__tcInstalled) return;
    window.__tcInstalled = true;

    function boot() { TreeChart.init(document); TreeChart.stagger(document); }

    document.addEventListener('DOMContentLoaded', boot);

    var observer = new MutationObserver(function () {
        clearTimeout(observer._t);
        observer._t = setTimeout(function () { TreeChart.init(document); }, 60);
    });
    observer.observe(document.body, { childList: true, subtree: true });

    window.addEventListener('resize', function () {
        clearTimeout(TreeChart._resizeT);
        TreeChart._resizeT = setTimeout(function () { TreeChart.updateHlines(); }, 120);
    });

    // Livewire SPA + morph support (no hard dependency on Livewire).
    document.addEventListener('livewire:navigated', boot);
    if (window.Livewire) {
        Livewire.hook('morph.updated', function () { setTimeout(boot, 60); });
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(boot, 0);
    }
})();
</script>
@endonce
