<?php

use Modules\Iqab\Controllers\Admin\Iqab;
use Modules\Iqab\Controllers\Admin\Rekapitulasi;

//----------------------------------Section Iqab-------------------------------------

//----------------------------------Default Iqab-------------------------------------
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

//-------------------------------------Admin Iqab -----------------------------------------------
$routes->group('iqab/admin', [
    'namespace' => 'Admin',
    'filter' => 'api-auth:admin',
], static function ($routes) {    
    $routes->add('/', [Iqab::class, 'index']);
    $routes->add('get', [Iqab::class, 'get']);
    $routes->add('store', [Iqab::class, 'store'], [ 'filter' => 'api-validation:daiq_list_iqab']);
    $routes->add('template', [Iqab::class, 'template']);
    $routes->add('upload', [Iqab::class, 'upload']);
    $routes->add('dashboard', [Iqab::class, 'dashboard']);
    $routes->add('status/(:any)/(:any)', [Iqab::class, 'status/$1/$2']);
    $routes->add('status_many', [Iqab::class, 'status_many']);
    $routes->add('delete/(:any)', [Iqab::class, 'delete/$1']);
    $routes->add('delete_many', [Iqab::class, 'delete_many']);
    $routes->add('download/(:any)', [Iqab::class, 'download/$1']);
    $routes->add('download_many', [Iqab::class, 'download_many']);
    $routes->group('rekapitulasi', static function ($routes) {
        $routes->add('/', [Rekapitulasi::class, 'index']);
        $routes->add('summary', [Rekapitulasi::class, 'summary']);
    });
});

//-----------------------------------------------------------------------------------------------------
