@php
    $downChildren = array_values(array_filter($node['children'], fn ($child) => $child['position'] !== 'side'));
    $sideChildren = array_values(array_filter($node['children'], fn ($child) => $child['position'] === 'side'));
    $hasDownChildren = count($downChildren) > 0;
    $isCollapsible = ($options['collapsible'] ?? true) && $hasDownChildren;
    $hideLabel = $node['header'] !== '' ? $node['header'] : ($node['label'] !== '' ? $node['label'] : $node['id']);
    $sideVisible = $node['has_side'] && (bool) $node['side_visible'];
    $photoEnabled = (bool) ($options['photo'] ?? true);
    $photoSrc = $photoEnabled
        ? ($node['has_photo'] ? $node['photo'] : ($options['photo_placeholder'] ?? null))
        : null;
    $photoAlt = $node['label'] !== '' ? $node['label'] : $node['header'];
@endphp

<div class="tc-node{{ $node['collapsed'] ? '' : ' tc-open' }}"
     data-tc-id="{{ $node['id'] }}"
     data-tc-dom="{{ $domId }}"
     data-tc-label="{{ $hideLabel }}"
     style="--tc-node-color:{{ $color }};">
    @if(! $isRoot)
        <div class="tc-up"></div>
    @endif

    <div class="tc-anchor-row">
        <div class="tc-anchor" style="width:{{ $width }}px;">
            <div class="tc-card" style="border-left:3px solid {{ $color }};">
                @if($node['header'] !== '')
                    <div class="tc-head" style="background:{{ $color }};">
                        <span class="tc-head-label">{{ $node['header'] }}</span>
                        @if($node['hideable'])
                            <button type="button" class="tc-btn-close" data-tc-hide="{{ $domId }}" title="Sembunyikan">&times;</button>
                        @endif
                    </div>
                @endif
                <div class="tc-body">
                    @if($photoSrc)
                        <div class="tc-photo{{ $node['has_photo'] ? ' tc-photo-clickable' : '' }}"
                             @if($node['has_photo']) data-tc-photo="{{ $photoSrc }}" data-tc-photo-alt="{{ $photoAlt }}" @endif>
                            <img src="{{ $photoSrc }}" alt="{{ $photoAlt }}" loading="lazy">
                        </div>
                    @endif
                    <div class="tc-body-text">
                        @if($node['label'] !== '')
                            <span class="tc-title">{{ $node['label'] }}</span>
                        @endif
                        @if($node['sub_label'] !== '')
                            <span class="tc-sub">{{ $node['sub_label'] }}</span>
                        @endif
                    </div>
                    <div class="tc-body-controls">
                        @if($node['has_side'] && ($options['side_toggle'] ?? true))
                            <label class="tc-switch" title="Tampilkan panel">
                                <input type="checkbox"
                                       data-tc-side="{{ $domId }}-side"
                                       {{ $sideVisible ? 'checked' : '' }}
                                       onclick="TreeChart.toggleSide(this)">
                                <span class="tc-slider"></span>
                            </label>
                        @endif
                        @if($isCollapsible)
                            <span class="tc-caret{{ $node['collapsed'] ? '' : ' tc-rotated' }}" data-tc-collapse></span>
                        @endif
                    </div>
                </div>
            </div>

            @if($node['has_side'])
                <div id="{{ $domId }}-side" class="tc-side{{ $sideVisible ? ' show' : '' }}">
                    <div class="tc-side-connector"></div>
                    <div class="tc-card tc-side-card" style="border-left:3px solid {{ $color }};">{!! $node['side'] !!}</div>
                </div>
            @endif
        </div>

        @foreach($sideChildren as $child)
            @php
                $childColor = ! empty($child['color'])
                    ? $child['color']
                    : ($palette[min($child['depth'], count($palette) - 1)] ?? '#6c757d');
                $childWidth = (int) ($child['width'] ?? $options['card_width'] ?? 260);
                $childDom = $domId.'-'.preg_replace('/[^a-zA-Z0-9_-]+/', '-', $child['id']);
            @endphp
            <div class="tc-side-node">
                <div class="tc-side-node-connector" style="--tc-connector-color:{{ $color }};"></div>
                @include('tree-chart::components.tree-node', [
                    'node' => $child,
                    'options' => $options,
                    'palette' => $palette,
                    'uid' => $uid,
                    'domId' => $childDom,
                    'color' => $childColor,
                    'width' => $childWidth,
                    'isRoot' => true,
                ])
            </div>
        @endforeach
    </div>

    @if($hasDownChildren)
        <div class="tc-collapse{{ $node['collapsed'] ? '' : ' tc-open' }}">
            <div class="tc-tree-children" style="--tc-children-color:{{ $color }};">
                <div class="tc-hline"></div>
                @foreach($downChildren as $child)
                    @php
                        $childColor = ! empty($child['color'])
                            ? $child['color']
                            : ($palette[min($child['depth'], count($palette) - 1)] ?? '#6c757d');
                        $childWidth = (int) ($child['width'] ?? $options['card_width'] ?? 260);
                        $childDom = $domId.'-'.preg_replace('/[^a-zA-Z0-9_-]+/', '-', $child['id']);
                    @endphp

                    @include('tree-chart::components.tree-node', [
                        'node' => $child,
                        'options' => $options,
                        'palette' => $palette,
                        'uid' => $uid,
                        'domId' => $childDom,
                        'color' => $childColor,
                        'width' => $childWidth,
                        'isRoot' => false,
                    ])
                @endforeach
            </div>
        </div>
    @endif
</div>
