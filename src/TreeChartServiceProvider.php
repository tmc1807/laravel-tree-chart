<?php

namespace Tmc\LaravelTreeChart;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Tmc\LaravelTreeChart\Components\TreeChart;

class TreeChartServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tree-chart.php', 'tree-chart');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tree-chart');

        Blade::component(TreeChart::class);

        $this->publishes([
            __DIR__.'/../config/tree-chart.php' => config_path('tree-chart.php'),
        ], 'tree-chart-config');

        if (config('tree-chart.demo', false)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/demo.php');
        }
    }
}
