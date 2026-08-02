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

/* Hide the native scrollbar when the sticky bottom bar takes over */
.tc-tree-chart[data-tc-scrollbar-bar] .tc-tree-scroll { scrollbar-width: none; }
.tc-tree-chart[data-tc-scrollbar-bar] .tc-tree-scroll::-webkit-scrollbar { height: 0; }

/* ===== Bottom-of-screen scrollbar ===== */
.tc-scrollbar {
    position: fixed; left: 0; right: 0; bottom: 0; z-index: 1000;
    display: none; padding: 4px 10px 6px;
    background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,.45) 50%, rgba(255,255,255,.8));
    opacity: 0; pointer-events: none; transition: opacity .25s ease;
}
.tc-scrollbar[data-tc-active] { display: block; }
.tc-scrollbar.show { opacity: 1; pointer-events: auto; }
.tc-scrollbar-track {
    position: relative; height: 5px; margin: 0 auto; max-width: 1200px;
    background: rgba(0,0,0,.08); border-radius: 3px;
}
.tc-scrollbar-thumb {
    position: absolute; top: 0; left: 0; height: 100%;
    min-width: 40px; background: rgba(0,0,0,.3); border-radius: 3px;
    cursor: grab; transition: background .15s ease;
}
.tc-scrollbar-thumb:hover { background: rgba(0,0,0,.45); }

/* ===== Row of siblings ===== */
.tc-tree-children {
    display: flex; flex-wrap: nowrap; justify-content: center; align-items: flex-start;
    gap: var(--tc-card-gap, 14px); position: relative; padding-top: 24px;
    transition: gap .35s cubic-bezier(.16,1,.3,1), padding-top .35s cubic-bezier(.16,1,.3,1);
}
.tc-tree-children::before {
    content: ''; position: absolute; top: calc(-1 * var(--tc-pad-top, 0px)); left: 50%; width: 2px;
    height: calc(20px + var(--tc-pad-top, 0px));
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
    transition: left .35s cubic-bezier(.16,1,.3,1), top .35s cubic-bezier(.16,1,.3,1), opacity .25s ease;
}
.tc-side-node-connector {
    position: absolute; left: -18px; top: 42px;
    width: 18px; height: 2px;
    border-top: 2px dashed var(--tc-connector-color, #6c757d);
}
.tc-tree-chart:not(.tc-connector-dashed) .tc-side-node-connector {
    border-top-style: solid;
}
.tc-side-node-connector::after {
    content: ''; position: absolute; left: -4px; top: -4px;
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--tc-connector-color, #6c757d); z-index: 2;
}
/* side-placed children only appear while their parent is expanded */
.tc-node:not(.tc-open) > .tc-anchor-row > .tc-side-node { display: none; }

.tc-card {
    border: 1px solid #e9ecef; border-radius: 10px; background: #fff;
    box-shadow: 0 1px 2px rgba(0,0,0,.06); overflow: hidden;
    min-height: var(--tc-card-height, auto);
    transition: box-shadow .15s ease, transform .35s cubic-bezier(.16,1,.3,1), opacity .3s ease, width .35s cubic-bezier(.16,1,.3,1), height .35s cubic-bezier(.16,1,.3,1), margin .35s cubic-bezier(.16,1,.3,1);
}
.tc-card:hover { box-shadow: 0 3px 8px rgba(0,0,0,.1); }

.tc-head {
    display: flex; justify-content: space-between; align-items: center;
    font-size: var(--tc-font-size, 11px); font-weight: 700; letter-spacing: .5px;
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
    padding: .5rem; display: flex; align-items: flex-start;
    justify-content: space-between; gap: .5rem; flex-direction: column; flex: 1;
}
.tc-body-text { min-width: 0; flex: 1; width: 100%; }
.tc-body-content { width: 100%; overflow: auto; }
.tc-photo {
    flex-shrink: 0; width: 44px; height: 44px; border-radius: 50%;
    overflow: hidden; background: #e9ecef; border: 2px solid var(--tc-node-color, #6c757d);
}
.tc-photo img { width: 100%; height: 100%; object-fit: cover; display: block; transition: opacity .15s ease; }
.tc-photo-clickable { cursor: zoom-in; }
.tc-photo-clickable:hover img { opacity: .85; }
.tc-title { font-size: var(--tc-font-size, 11px); line-height: 1.3; font-weight: 600; color: #212529; display: block; }
.tc-sub { font-size: calc(var(--tc-font-size, 11px) * 0.91); color: #6c757d; display: block; margin-top: 2px; }
.tc-body-controls { display: flex; align-items: center; gap: .35rem; flex-shrink: 0; }

.tc-caret {
    width: 0; height: 0;
    border-left: 7px solid transparent;
    border-right: 7px solid transparent;
    border-top: 12px solid var(--tc-node-color, #6c757d);
    transition: transform .2s ease; flex-shrink: 0; cursor: pointer;
}
.tc-caret.tc-rotated { transform: rotate(180deg); }

/* ===== Side panel ===== */
.tc-side {
    position: absolute; top: 0; left: 100%; display: none; align-items: flex-start;
    z-index: 3; width: var(--tc-side-width, 500px);
}
.tc-side.show { display: flex; }
.tc-side-connector {
    width: 18px; height: 2px; flex-shrink: 0; position: relative;
    border-top: 2px dashed var(--tc-node-color, #6c757d);
    transition: width .35s cubic-bezier(.16,1,.3,1), opacity .3s ease;
}
.tc-tree-chart:not(.tc-connector-dashed) .tc-side-connector {
    border-top-style: solid;
}
.tc-side-connector::after {
    content: ''; position: absolute; left: -4px; top: -4px;
    width: 8px; height: 8px; border-radius: 50%; background: var(--tc-node-color, #6c757d); z-index: 2;
}
.tc-side-card { width: 100%; }

/* ===== Collapse ===== */
.tc-collapse { display: none; transition: margin-top .35s cubic-bezier(.16,1,.3,1); }
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

/* ===== Photo lightbox ===== */
.tc-lightbox {
    position: fixed; inset: 0; z-index: 10000;
    display: flex; align-items: center; justify-content: center;
    background: rgba(0,0,0,.72);
    opacity: 0; pointer-events: none; transition: opacity .2s ease;
}
.tc-lightbox.show { opacity: 1; pointer-events: auto; }
.tc-lightbox-figure { position: relative; max-width: 90vw; max-height: 90vh; margin: 0; }
.tc-lightbox-figure img {
    display: block; max-width: 90vw; max-height: 84vh;
    border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,.5);
    background: #fff;
}
.tc-lightbox-caption {
    margin-top: 10px; text-align: center; color: #fff;
    font-size: .85rem; font-weight: 600; line-height: 1.3;
}
.tc-lightbox-close {
    position: fixed; top: 16px; right: 16px; width: 40px; height: 40px;
    border: none; border-radius: 50%; background: rgba(255,255,255,.15); color: #fff;
    font-size: 1.5rem; line-height: 1; cursor: pointer;
    transition: background .15s ease;
}
.tc-lightbox-close:hover { background: rgba(255,255,255,.3); }

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
    animation: tcFadeOut .4s cubic-bezier(.4,0,.2,1) both;
}
</style>
@endonce
