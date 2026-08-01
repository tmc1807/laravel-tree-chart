<?php

use Illuminate\Support\Facades\Route;

Route::get('/tree-chart/demo', function () {
    return view('tree-chart::demo');
})->name('tree-chart.demo');
