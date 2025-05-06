<?php

use Modules\Sholat\Controllers\Sholat;
use Modules\Sholat\Controllers\Wajib\Sholat as SholatWajib;
use Modules\Sholat\Controllers\Wajib\Rekapitulasi as SholatRekapitulasi;

//----------------------------------Section Tabungan-------------------------------------

// //----------------------------------Default Tabungan-------------------------------------
// $routes->group('sholat', static function ($routes) {
//     $routes->add('/', [Sholat::class,'get']);
//     $routes->add('get', [Sholat::class,'get']);
//     $routes->add('search', [Sholat::class,'search']);
//     $routes->add('store', [Sholat::class,'store'],[ 'filter' => 'api-validation:mu_sholat']);
//     $routes->add('template', [Sholat::class,'template']);
//     $routes->add('upload', [Sholat::class,'upload']);
//     $routes->add('preparation', [Sholat::class,'preparation']);
// });

//-------------------------------------Wajib Sholat -----------------------------------------------
$routes->group('sholat/wajib', [
    'filter' => 'api-auth',
], static function ($routes) {    
    $routes->add('/', [SholatWajib::class,'index']);
    $routes->add('get', [SholatWajib::class, 'get']);
    $routes->add('get_where', [SholatWajib::class, 'get_where']);
    $routes->add('store', [SholatWajib::class, 'store'], [ 'filter' => 'api-validation:mu_sholat-wajib']);
    $routes->add('dashboard', [SholatWajib::class, 'dashboard']);
});

//-----------------------------------------------------------------------------------------------------

