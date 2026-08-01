@once('tree-chart-scripts')
<script>
window.TreeChart = (function () {
    var SIDE_HIDING = 320;
    var COLLAPSE_HIDING = 700;
    var HIDE_NODE_HIDING = 480;

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

        // Failsafe: entrance animations can freeze at the first frame in some
        // real-browser conditions (occluded/tab energy saver), leaving cards
        // stuck transparent. Force any card still below full opacity to settle.
        if (scope._tcStaggerT) clearTimeout(scope._tcStaggerT);
        scope._tcStaggerT = setTimeout(function () {
            if (scope === document) {
                document.querySelectorAll('.tc-tree-chart').forEach(function (c) {
                    TreeChart.settleAnimations(c);
                });
            } else {
                TreeChart.settleAnimations(scope);
            }
        }, 2000);
    }

    function hideCollapse(node, done) {
        var collapse = node.querySelector(':scope > .tc-collapse');
        if (!collapse) return done();
        var children = collapse.querySelector('.tc-tree-children');
        var cards = collapse.querySelectorAll('.tc-card, .tc-up, .tc-hline, .tc-tree-children');
        cards.forEach(function (el) {
            // Clear any inline `animation` override so the CSS fade-out always runs.
            el.style.animation = '';
            el.style.removeProperty('--tc-delay');
            el.classList.add('tc-hiding');
        });
        // Animate collapse container height for smooth closing
        if (children) {
            var h = children.offsetHeight;
            children.style.height = h + 'px';
            children.style.overflow = 'hidden';
            children.style.transition = 'height 400ms cubic-bezier(.4,0,.2,1), opacity 320ms cubic-bezier(.4,0,.2,1)';
            // Force reflow
            void children.offsetHeight;
            children.style.height = '0';
            children.style.opacity = '0';
        }
        setTimeout(function () {
            collapse.classList.remove('tc-open');
            node.classList.remove('tc-open');
            collapse.querySelectorAll('.tc-hiding').forEach(function (el) {
                el.classList.remove('tc-hiding');
                el.style.removeProperty('--tc-delay');
            });
            if (children) {
                children.style.height = '';
                children.style.overflow = '';
                children.style.transition = '';
                children.style.opacity = '';
            }
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

            var scroll = chart.querySelector('.tc-tree-scroll');
            var bar = chart.querySelector('[data-tc-scrollbar]');
            if (scroll && bar) {
                chart.setAttribute('data-tc-scrollbar-bar', '1');
                var track = bar.querySelector('[data-tc-scrollbar-track]');
                var thumb = bar.querySelector('[data-tc-scrollbar-thumb]');
                var hideTimer = null;

                function ping() {
                    if (bar.getAttribute('data-tc-active') !== '1') return;
                    bar.classList.add('show');
                    clearTimeout(hideTimer);
                    hideTimer = setTimeout(function () { bar.classList.remove('show'); }, 1200);
                }

                scroll.addEventListener('scroll', function () { TreeChart.updateScrollbar(scroll); ping(); });
                scroll.addEventListener('mouseenter', ping);
                scroll.addEventListener('mousemove', ping);
                scroll.addEventListener('mouseleave', function () { bar.classList.remove('show'); });

                if (track && thumb) {
                    thumb.addEventListener('pointerdown', function (e) {
                        e.preventDefault();
                        ping();
                        var startX = e.clientX;
                        var startSL = scroll.scrollLeft;
                        var range = scroll.scrollWidth - scroll.clientWidth;
                        var tRange = Math.max(1, track.clientWidth - thumb.offsetWidth);
                        function move(ev) {
                            scroll.scrollLeft = Math.max(0, Math.min(range, startSL + ((ev.clientX - startX) / tRange) * range));
                        }
                        function up() {
                            window.removeEventListener('pointermove', move);
                            window.removeEventListener('pointerup', up);
                            setTimeout(function () { bar.classList.remove('show'); }, 1200);
                        }
                        window.addEventListener('pointermove', move);
                        window.addEventListener('pointerup', up);
                    });
                }
            }

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

        intersectViewport: function (el) {
            var r = el.getBoundingClientRect();
            return r.bottom > 0 && r.top < window.innerHeight && r.right > 0 && r.left < window.innerWidth;
        },

        syncBars: function () {
            document.querySelectorAll('.tc-tree-chart').forEach(function (chart) {
                var scroll = chart.querySelector('.tc-tree-scroll');
                var bar = chart.querySelector('[data-tc-scrollbar]');
                if (!scroll || !bar) return;
                if (scroll.scrollWidth > scroll.clientWidth + 2 && TreeChart.intersectViewport(scroll)) {
                    bar.setAttribute('data-tc-active', '1');
                } else {
                    bar.removeAttribute('data-tc-active');
                    bar.classList.remove('show');
                }
            });
        },

        updateScrollbar: function (scroll) {
            if (!scroll) return;
            var chart = closest(scroll, '.tc-tree-chart');
            var bar = chart ? chart.querySelector('[data-tc-scrollbar]') : null;
            if (!bar) return;
            var track = bar.querySelector('[data-tc-scrollbar-track]');
            var thumb = bar.querySelector('[data-tc-scrollbar-thumb]');
            if (!track || !thumb) return;

            if (scroll.scrollWidth <= scroll.clientWidth + 2) {
                bar.removeAttribute('data-tc-active');
                bar.classList.remove('show');
                return;
            }

            var tw = track.clientWidth;
            var thumbW = Math.max(40, tw * scroll.clientWidth / scroll.scrollWidth);
            thumb.style.width = thumbW + 'px';

            var range = scroll.scrollWidth - scroll.clientWidth;
            var tRange = tw - thumbW;
            thumb.style.left = (range > 0 ? (scroll.scrollLeft / range) * tRange : 0) + 'px';

            TreeChart.syncBars();
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

                    var sideLeft = Math.max(cardRight + gap - overhang, requiredRight + gap);
                    var cardLeft = sideLeft + overhang;

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
                    var siblingRow = node.parentElement
                        && node.parentElement.classList.contains('tc-tree-children');
                    if (reserve > 0 && siblingRow && node.nextElementSibling) {
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
                tree.style.marginLeft = '';
                tree.style.marginRight = '';
                tree.style.boxSizing = '';
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
                if (padL > 0) {
                    // content-box + left-align so the padding shifts the content
                    // fully right (border-box + centered flex would eat half of it)
                    tree.style.boxSizing = 'content-box';
                    tree.style.marginLeft = '0';
                    tree.style.marginRight = '0';
                    tree.style.paddingLeft = padL + 'px';
                    void tree.offsetHeight;
                }

                treeRect = tree.getBoundingClientRect();
                b = cardBounds();
                var padR = Math.max(0, Math.round(b.maxR - treeRect.right));
                if (padR > 0) {
                    tree.style.boxSizing = 'content-box';
                    tree.style.paddingRight = padR + 'px';
                    void tree.offsetHeight;
                }

                // Scroll to reveal the rightmost card so side panels
                // and wide subtrees are never cut off at the viewport edge.
                var sr = scroll.getBoundingClientRect();
                var need = Math.ceil(b.maxR - sr.right);
                if (need > 1) scroll.scrollLeft += need;

                TreeChart.updateScrollbar(scroll);
            });
        },

        revealCollapseCards: function (collapse) {
            if (!collapse) return;
            var scroll = closest(collapse, '.tc-tree-scroll');
            if (!scroll) return;
            var chart = closest(collapse, '.tc-tree-chart');
            if (!chart) return;
            var cards = chart.querySelectorAll('.tc-card');
            var rightmost = null;
            var maxRight = -Infinity;
            for (var i = 0; i < cards.length; i++) {
                var r = cards[i].getBoundingClientRect();
                if (r.width <= 0 || r.height <= 0) continue;
                if (r.right > maxRight) {
                    maxRight = r.right;
                    rightmost = cards[i];
                }
            }
            if (!rightmost) return;
            rightmost.scrollIntoView({ block: 'nearest', inline: 'nearest' });
            var lr = rightmost.getBoundingClientRect();
            var sr = scroll.getBoundingClientRect();
            var need = Math.ceil(lr.right - sr.right);
            if (need > 1) scroll.scrollLeft += need;
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

        settleAnimations: function (root) {
            var chart = (root && root.classList && root.classList.contains('tc-tree-chart'))
                ? root : closest(root, '.tc-tree-chart');
            if (!chart || !chart.querySelectorAll) return;
            chart.querySelectorAll('.tc-card, .tc-up, .tc-hline').forEach(function (el) {
                if (el.classList.contains('tc-hiding')) return;
                if (parseFloat(getComputedStyle(el).opacity) >= 0.99) return;
                if (el.getAnimations) {
                    var anims = el.getAnimations();
                    for (var i = 0; i < anims.length; i++) {
                        var a = anims[i];
                        if (a.animationName !== 'tcCardIn' && a.animationName !== 'tcLineIn') continue;
                        if (a.playState === 'running' || a.playState === 'paused') {
                            try { a.finish(); } catch (e) {}
                        }
                    }
                }
                if (parseFloat(getComputedStyle(el).opacity) < 0.99) {
                    el.style.animation = 'none';
                    el.style.removeProperty('--tc-delay');
                }
            });
        },

        toggleCollapse: function (node) {
            if (!node) return;
            var collapse = node.querySelector(':scope > .tc-collapse');
            if (!collapse) return;

            if (collapse.classList.contains('tc-open')) {
                hideCollapse(node, function () {
                    setTimeout(function () {
                        TreeChart.updateHlines();
                        TreeChart.settleAnimations(node);
                    }, 30);
                });
            } else {
                collapse.classList.add('tc-open');
                node.classList.add('tc-open');
                stagger(collapse);
                setTimeout(function () {
                    TreeChart.updateHlines();
                    setTimeout(function () {
                        TreeChart.updateHlines();
                        TreeChart.revealCollapseCards(collapse);
                        setTimeout(function () { TreeChart.revealCollapseCards(collapse); }, 200);
                    }, 30);
                }, 30);
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
                setTimeout(function () {
                    TreeChart.updateHlines();
                    setTimeout(function () {
                        TreeChart.updateHlines();
                        var scroll = chart ? chart.querySelector('.tc-tree-scroll') : null;
                        if (scroll) {
                            var cards = chart.querySelectorAll('.tc-card');
                            var maxRight = -Infinity;
                            for (var i = 0; i < cards.length; i++) {
                                var r = cards[i].getBoundingClientRect();
                                if (r.width <= 0 || r.height <= 0) continue;
                                if (r.right > maxRight) maxRight = r.right;
                            }
                            if (maxRight > -Infinity) {
                                var sr = scroll.getBoundingClientRect();
                                var need = Math.ceil(maxRight - sr.right);
                                if (need > 1) scroll.scrollLeft += need;
                            }
                        }
                    }, 30);
                }, 20);
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
                    setTimeout(function () {
                        TreeChart.updateHlines();
                        TreeChart.settleAnimations(chart);
                    }, 20);
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
                TreeChart.settleAnimations(chart);
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
        TreeChart._resizeT = setTimeout(function () { TreeChart.updateHlines(); TreeChart.syncBars(); }, 120);
    });

    window.addEventListener('scroll', function () {
        clearTimeout(TreeChart._scrollbarT);
        TreeChart._scrollbarT = setTimeout(function () { TreeChart.syncBars(); }, 60);
    }, true);

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
