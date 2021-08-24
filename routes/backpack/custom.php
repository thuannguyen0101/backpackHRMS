<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

use App\Http\Controllers\Admin\DemoController;
use App\Http\Controllers\Admin\SchoolsCrudController;
use App\Http\Controllers\Admin\SystemProjectController;
use App\Http\Controllers\API\ControllerSelectSchool;




Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes

    Route::crud('tag', 'TagCrudController');
    Route::crud('product', 'ProductCrudController');
    Route::crud('schools', 'SchoolsCrudController');
    Route::crud('classes', 'ClassesCrudController');
    Route::crud('students', 'StudentsCrudController');
    Route::get('school/{id}/update', [SchoolsCrudController::class,'update_status'])->name('update_status');
    Route::get('schools/select', [SchoolsCrudController::class,'getSchoolList']);
    Route::get('/schools/{id}/classes', [SystemProjectController::class,'index']);



}); // this should be the absolute last line of this file
