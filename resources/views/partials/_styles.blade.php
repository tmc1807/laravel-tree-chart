@once('tree-chart-styles')
<style>
/* ===== tree-chart: self-contained, framework-agnostic ===== */
.tc-tree-chart {
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: #212529;
    line-height: 1.4;
}
.tc-tree-chart * { box-sizing: border-box; }

.tc-tree-chart .tc-header { text-align: center; margin-bottom: .5rem; }
.tc-tree-chart .tc-title-lg { margin: 0 0 .1rem; font-size: 1.1rem; font-weight: 700; }
.tc-tree-chart .tc-subtitle { color: #6c757d; font-size: .8rem; }

/* ===== Scroll container ===== */
.tc-tree-scroll {
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-gutter: stable;
    scrollbar-width: thin;
    position: relative;
    width: 100%;
    box-sizing: border-box;
    contain: layout;
    min-height: 240px;
    padding-bottom: 80px;
    margin-bottom: 80px;
}
.tc-tree {
    display: flex; flex-direction: column; align-items: center;
    width: max-content;
    min-width: 100%;
    margin: 0 auto;
    margin-bottom: 0;
    padding-right: 80px;
}

/* Ensure scrollbar extends to viewport edge */
.tc-tree-scroll::-webkit-scrollbar {
    height: 8px;
}
.tc-tree-scroll::-webkit-scrollbar-thumb {
    background: #adb5bd;
    border-radius: 4px;
}
.tc-tree-scroll::-webkit-scrollbar-track {
    background: transparent;
}

/* ===== Row of siblings ===== */
.tc-tree-children {
    display: flex; flex-wrap: nowrap; justify-content: center; align-items: flex-start;
    gap: 14px; position: relative; padding-top: 24px;
    transition: gap .35s cubic-bezier(.16,1,.3,1), padding-top .35s cubic-bezier(.16,1,.3,1);
}
.tc-tree-children::before {
    content: ''; position: absolute; top: 0; left: 50%; width: 2px; height: 20px;
    background: var(--tc-children-color, #6c757d);
    transform: translateX(-1px);
}
.tc-tree-children::after {
    content: ''; position: absolute; top: 20px; left: 50%; width: 8px; height: 8px;
    border-radius: 50%; background: var(--tc-children-color, #6c757d);
    transform: translateX(-4px); z-index: 2;
}
.tc-tree-chart.tc-connector-dashed .tc-tree-children::before {
    background: repeating-linear-gradient(to bottom, var(--tc-children-color, #6c757d) 0, var(--tc-children-color, #6c757d) 6px, transparent 6px, transparent 10px);
}

/* horizontal line bridging siblings */
.tc-hline {
    height: 2px; position: absolute; top: 24px; left: 0; width: 0; right: auto;
    z-index: 1; display: none; pointer-events: none;
    background: var(--tc-children-color, #6c757d);
    transition: left .35s cubic-bezier(.16,1,.3,1), width .35s cubic-bezier(.16,1,.3,1), opacity .3s ease;
}
.tc-tree-chart.tc-connector-dashed .tc-hline {
    background: repeating-linear-gradient(to right, var(--tc-children-color, #6c757d) 0, var(--tc-children-color, #6c757d) 6px, transparent 6px, transparent 10px);
}

/* ===== Node column ===== */
.tc-node {
    display: flex; flex-direction: column; align-items: center;
    position: relative; flex-shrink: 0;
    transition: margin-right .35s cubic-bezier(.16,1,.3,1), transform .35s cubic-bezier(.16,1,.3,1);
}
.tc-node.tc-hidden { display: none !important; }

/* vertical line from child up to parent */
.tc-up {
    width: 2px; height: 24px; flex-shrink: 0; position: relative;
    background: var(--tc-node-color, #6c757d);
    transition: opacity .3s ease, transform .35s cubic-bezier(.16,1,.3,1);
}
.tc-tree-chart.tc-connector-dashed .tc-up {
    background: repeating-linear-gradient(to bottom, var(--tc-node-color, #6c757d) 0, var(--tc-node-color, #6c757d) 6px, transparent 6px, transparent 10px);
}
.tc-up::before {
    content: ''; position: absolute; top: -4px; left: 50%; width: 8px; height: 8px;
    border-radius: 50%; background: var(--tc-node-color, #6c757d);
    transform: translateX(-4px); z-index: 2;
}

/* anchor wraps the card so absolute side panel never shifts column width */
.tc-anchor { position: relative; }
.tc-anchor .tc-card { width: 100%; }

/* row holding the parent card + side-placed children */
.tc-anchor-row { display: flex; align-items: flex-start; position: relative; transition: transform .35s cubic-bezier(.16,1,.3,1); }
.tc-side-node {
    position: absolute; left: 100%; top: 0;
    display: flex; align-items: flex-start; z-index: 2;
    transition: left .35s cubic-bezier(.16,1,.3,1), opacity .25s ease;
}
.tc-side-node-connector {
    position: absolute; left: -18px; top: 42px;
    width: 18px; height: 2px;
    border-top: 2px dashed var(--tc-connector-color, #6c757d);
}
.tc-side-node-connector::after {
    content: ''; position: absolute; right: -4px; top: -4px;
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--tc-connector-color, #6c757d); z-index: 2;
}
/* side-placed children only appear while their parent is expanded */
.tc-node:not(.tc-open) > .tc-anchor-row > .tc-side-node { display: none; }

.tc-card {
    border: 1px solid #e9ecef; border-radius: 10px; background: #fff;
    box-shadow: 0 1px 2px rgba(0,0,0,.06); overflow: hidden;
    transition: box-shadow .15s ease, transform .35s cubic-bezier(.16,1,.3,1), opacity .3s ease, width .35s cubic-bezier(.16,1,.3,1), height .35s cubic-bezier(.16,1,.3,1), margin .35s cubic-bezier(.16,1,.3,1);
}
.tc-card:hover { box-shadow: 0 3px 8px rgba(0,0,0,.1); }

.tc-head {
    display: flex; justify-content: space-between; align-items: center;
    font-size: .68rem; font-weight: 700; letter-spacing: .5px;
    padding: .28rem .5rem; background: #495057; color: #fff;
    border-radius: 10px 10px 0 0;
    text-transform: uppercase;
}
.tc-head-label { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.tc-btn-close {
    background: none; border: none; color: #fff; font-size: .85rem;
    line-height: 1; padding: 0 0 0 .4rem; opacity: .85; cursor: pointer;
}
.tc-btn-close:hover { opacity: 1; }

.tc-body {
    padding: .5rem; display: flex; align-items: center;
    justify-content: space-between; gap: .5rem; cursor: pointer;
}
.tc-body-text { min-width: 0; flex: 1; }
.tc-photo {
    flex-shrink: 0; width: 44px; height: 44px; border-radius: 50%;
    overflow: hidden; background: #e9ecef; border: 2px solid var(--tc-node-color, #6c757d);
}
.tc-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
.tc-title { font-size: .68rem; line-height: 1.3; font-weight: 600; color: #212529; display: block; }
.tc-sub { font-size: .62rem; color: #6c757d; display: block; margin-top: 2px; }
.tc-badge {
    display: inline-block; font-size: .58rem; font-weight: 700; color: #fff;
    padding: .12rem .4rem; border-radius: 10px; margin-top: 4px;
    white-space: nowrap;
}
.tc-body-controls { display: flex; align-items: center; gap: .35rem; flex-shrink: 0; }

.tc-caret {
    display: inline-flex; align-items: center; justify-content: center;
    width: 20px; height: 20px; border-radius: 50%; color: #fff;
    transition: transform .2s ease; flex-shrink: 0; cursor: pointer;
}
.tc-caret svg { width: 11px; height: 11px; }
.tc-node.tc-open > .tc-anchor .tc-caret { transform: rotate(180deg); }

/* ===== Side panel ===== */
.tc-side {
    position: absolute; top: 0; left: 100%; display: none; align-items: center;
    z-index: 3; width: var(--tc-side-width, 500px);
}
.tc-side.show { display: flex; }
.tc-side-connector {
    width: 18px; height: 2px; flex-shrink: 0; position: relative;
    border-top: 2px dashed var(--tc-node-color, #6c757d);
    transition: width .35s cubic-bezier(.16,1,.3,1), opacity .3s ease;
}
.tc-side-connector::after {
    content: ''; position: absolute; right: -4px; top: -4px;
    width: 8px; height: 8px; border-radius: 50%; background: var(--tc-node-color, #6c757d); z-index: 2;
}
.tc-side-card { width: 100%; }

/* ===== Collapse ===== */
.tc-collapse { display: none; }
.tc-collapse.tc-open { display: block; animation: tcCollapseIn .35s cubic-bezier(.16,1,.3,1) both; }
@keyframes tcCollapseIn {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ===== Switch (pure CSS checkbox) ===== */
.tc-switch { position: relative; display: inline-block; width: 30px; height: 17px; cursor: pointer; flex-shrink: 0; }
.tc-switch input { opacity: 0; width: 0; height: 0; }
.tc-switch .tc-slider {
    position: absolute; inset: 0; background: #adb5bd; border-radius: 17px; transition: background .2s ease;
}
.tc-switch .tc-slider::before {
    content: ''; position: absolute; width: 13px; height: 13px; left: 2px; top: 2px;
    border-radius: 50%; background: #fff; transition: transform .2s ease;
}
.tc-switch input:checked + .tc-slider { background: #20c997; }
.tc-switch input:checked + .tc-slider::before { transform: translateX(13px); }

/* ===== Hidden-node badges (rendered near the tree root) ===== */
.tc-hidden-badges { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; justify-content: center; }
.tc-hidden-badge {
    display: inline-flex; align-items: center; gap: 4px; cursor: pointer;
    background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 20px;
    padding: 2px 10px; font-size: .66rem; color: #495057; font-weight: 600;
    transition: background .15s ease;
}
.tc-hidden-badge:hover { background: #e9ecef; }
.tc-hidden-badge .tc-x { color: #dc3545; font-weight: 700; }

/* ===== Animations (staggered) ===== */
@keyframes tcCardIn {
    from { opacity: 0; transform: translateY(16px) scale(0.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes tcLineIn {
    from { opacity: 0; transform: scaleY(0.3); }
    to   { opacity: 1; transform: scaleY(1); }
}
@keyframes tcFadeOut {
    from { opacity: 1; transform: scale(1); }
    to   { opacity: 0; transform: scale(0.96); }
}
.tc-tree-chart.tc-animate .tc-card {
    animation: tcCardIn .5s cubic-bezier(.16,1,.3,1) both;
    animation-delay: var(--tc-delay, 0s);
}
.tc-tree-chart.tc-animate .tc-up,
.tc-tree-chart.tc-animate .tc-hline {
    animation: tcLineIn .5s cubic-bezier(.16,1,.3,1) both;
    animation-delay: var(--tc-delay, 0s);
    transform-origin: top center;
}
.tc-tree-chart.tc-animate .tc-tree-children::before,
.tc-tree-chart.tc-animate .tc-tree-children::after {
    animation: tcLineIn .5s cubic-bezier(.16,1,.3,1) both;
    animation-delay: var(--tc-delay, 0s);
    transform-origin: top center;
}
.tc-tree-chart.tc-animate .tc-card.tc-hiding,
.tc-tree-chart.tc-animate .tc-up.tc-hiding,
.tc-tree-chart.tc-animate .tc-hline.tc-hiding,
.tc-tree-chart.tc-animate .tc-tree-children.tc-hiding::before,
.tc-tree-chart.tc-animate .tc-tree-children.tc-hiding::after,
.tc-tree-chart.tc-animate .tc-side-connector.tc-hiding {
    animation: tcFadeOut .25s cubic-bezier(.4,0,1,1) both;
}
</style>
@endonce
