@include('tree-chart::partials._styles')

@php
    $palette = (array) ($options['colors'] ?? []);
    $connectorClass = ($options['connector'] ?? 'dashed') === 'dashed' ? ' tc-connector-dashed' : '';
    $scrollable = (bool) ($options['scrollable'] ?? true);
    $stickySb = $scrollable && (bool) ($options['sticky_scrollbar'] ?? true);
@endphp

<div class="tc-tree-chart{{ $options['animate'] ? ' tc-animate' : '' }}{{ $connectorClass }}"
     data-tc-uid="{{ $uid }}"
     data-tc-side-width="{{ $options['side_width'] ?? 500 }}"
     style="--tc-side-width:{{ $options['side_width'] ?? 500 }}px;">

    @if(! empty($options['title']) || ! empty($options['subtitle']))
        <div class="tc-header">
            @if(! empty($options['title']))<h5 class="tc-title-lg">{{ $options['title'] }}</h5>@endif
            @if(! empty($options['subtitle']))<small class="tc-subtitle">{{ $options['subtitle'] }}</small>@endif
        </div>
    @endif

    @if($scrollable)
    <div class="tc-tree-scroll">
    @endif
        <div class="tc-tree">
            @foreach($nodes as $node)
                @php
                    $color = ! empty($node['color'])
                        ? $node['color']
                        : ($palette[0] ?? '#6c757d');
                    $width = (int) ($node['width'] ?? $options['card_width'] ?? 260);
                    $domId = 'tc-'.$uid.'-'.preg_replace('/[^a-zA-Z0-9_-]+/', '-', $node['id']);
                @endphp

                @include('tree-chart::components.tree-node', [
                    'node' => $node,
                    'options' => $options,
                    'palette' => $palette,
                    'uid' => $uid,
                    'domId' => $domId,
                    'color' => $color,
                    'width' => $width,
                    'isRoot' => true,
                ])
            @endforeach
        </div>
    @if($scrollable)
    </div>
    @endif

    @if($stickySb)
    <div class="tc-tree-scrollbar" data-tc-scrollbar>
        <div class="tc-tree-scrollbar-thumb"></div>
    </div>
    @endif

    <div class="tc-hidden-badges" data-tc-hidden-badges></div>
</div>

@include('tree-chart::partials._scripts')
