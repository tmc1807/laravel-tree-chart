<?php

namespace Tmc\LaravelTreeChart\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Tmc\LaravelTreeChart\TreeChartServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            TreeChartServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        view()->addLocation(__DIR__.'/resources/views');

        $path = app('path.storage').'/framework/views';
        if (is_dir($path)) {
            foreach (glob($path.'/*') as $file) {
                @unlink($file);
            }
        }
    }
}
