<?php

// use Modules\Library\Controllers\Psb;
use Modules\Library\Controllers\Admin\Buku;
use Modules\Library\Controllers\Admin\JenisBuku;

//----------------------------------Section PSB-------------------------------------

// //----------------------------------Default PSB-------------------------------------
// $routes->group('psb', static function ($routes) {
//     $routes->add('/', [Psb::class,'get']);
//     $routes->add('get', [Psb::class,'get']);
//     $routes->add('search', [Psb::class,'search']);
//     $routes->add('store', [Psb::class,'store'],[ 'filter' => 'api-validation:mu_psb']);
//     $routes->add('template', [Psb::class,'template']);
//     $routes->add('upload', [Psb::class,'upload']);
//     $routes->add('preparation', [Psb::class,'preparation']);
// });

//-------------------------------------Admin PSB -----------------------------------------------
$routes->group('library/admin', [
    'filter' => 'api-auth:admin',
], static function ($routes) {    
    $routes->add('/', [Buku::class,'index']);
    $routes->add('get', [Buku::class,'get']);
    $routes->add('store', [Buku::class,'store'],[ 'filter' => 'api-validation:dalib_buku']);
    $routes->add('dashboard', [Buku::class,'dashboard']);
    $routes->add('status/(:any)/(:any)', [Buku::class,'status/$1/$2']);
    $routes->add('status_many', [Buku::class,'status_many']);
    $routes->add('delete/(:any)', [Buku::class,'delete/$1']);
    $routes->add('delete_many', [Buku::class,'delete_many']);
    $routes->add('download/(:any)', [Buku::class,'download/$1']);
    $routes->add('download_many', [Buku::class,'download_many']);

        
    //----------------------------------Data Jenis Buku-------------------------------------
    $routes->group('jenis', static function ($routes) {
        $routes->add('/', [JenisBuku::class, 'index']);
        $routes->add('get', [JenisBuku::class, 'get']);
        $routes->add('store', [JenisBuku::class, 'store'], [ 'filter' => 'api-validation:dalib_jenis_buku']);
        $routes->add('delete/(:any)', [JenisBuku::class, 'delete/$1']);
        $routes->add('delete_many', [JenisBuku::class, 'delete_many']);
        $routes->add('template', [JenisBuku::class, 'template']);
        $routes->add('upload', [JenisBuku::class, 'upload']);
        $routes->add('reset_options', [JenisBuku::class, 'resetOptions']);
    });
});

//-----------------------------------------------------------------------------------------------------

