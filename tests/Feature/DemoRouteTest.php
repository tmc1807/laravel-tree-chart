<?php

namespace Tmc\LaravelTreeChart\Tests\Feature;

use Tmc\LaravelTreeChart\Tests\TestCase;

class DemoRouteTest extends TestCase
{
    protected function defineEnvironment($app)
    {
        $app['config']->set('tree-chart.demo', true);
    }

    public function test_demo_route_is_served_when_enabled()
    {
        $this->get('/tree-chart/demo')
            ->assertOk()
            ->assertSee('laravel-tree-chart');
    }
}
