<?php

it('renders the demo page without errors', function () {
    $html = view('tree-chart::demo')->render();

    expect($html)
        ->toContain('laravel-tree-chart')
        ->toContain('tc-tree-chart')
        ->toContain('Indikator Tujuan')
        ->toContain('tc-side show');
});

it('does not register the demo route by default', function () {
    $routes = app('router')->getRoutes();

    expect($routes->hasNamedRoute('tree-chart.demo'))->toBeFalse();
});
