<?php

use Modules\Quran\Controllers\Baca\QuranController as Quran;
// use Modules\Quran\Controllers\Baca\Rekapitulasi;

//----------------------------------Section Quran-------------------------------------

//----------------------------------Default Quran-------------------------------------
// $routes->group('psb', [
//     'namespace' => 'App\Controllers\Psb',
// ], static function ($routes) {
//     $routes->add('/', 'Psb::index');
//     $routes->add('get', 'Psb::get');
//     $routes->add('search', 'Psb::search');
//     $routes->add('store', 'Psb::store');
//     $routes->add('template', 'Psb::template');
//     $routes->add('upload', 'Psb::upload');
//     $routes->add('preparation', 'Psb::preparation');
// });

//------------------------------------- Baca Quran -----------------------------------------------
$routes->group('quran/baca', [
    'namespace' => 'Baca',
    'filter' => 'api-auth',
], static function ($routes) {    
    $routes->add('/', [Quran::class, 'index']);
    $routes->add('get', [Quran::class, 'get']);
    $routes->add('get_last', [Quran::class, 'get_last']);
    $routes->add('store', [Quran::class, 'save'], [ 'filter' => 'api-validation:mu_quran_baca']);
    $routes->add('template', [Quran::class, 'template']);
    $routes->add('upload', [Quran::class, 'upload']);
    $routes->add('dashboard', [Quran::class, 'dashboard']);
    $routes->add('status/(:any)/(:any)', [Quran::class, 'status/$1/$2']);
    $routes->add('status_many', [Quran::class, 'status_many']);
    $routes->add('delete/(:any)', [Quran::class, 'delete/$1']);
    $routes->add('delete_many', [Quran::class, 'delete_many']);
    $routes->add('download/(:any)', [Quran::class, 'download/$1']);
    $routes->add('download_many', [Quran::class, 'download_many']);
    // $routes->group('rekapitulasi', static function ($routes) {
    //     $routes->add('/', [Rekapitulasi::class, 'index']);
    //     $routes->add('summary', [Rekapitulasi::class, 'summary']);
    // });
});

//-----------------------------------------------------------------------------------------------------
