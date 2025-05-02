<?php

use Modules\Data\Controllers\Pcm;
use Modules\Data\Controllers\Prm;
use Modules\Data\Controllers\Anggota;
use Modules\Data\Controllers\Iqab;
use Modules\Data\Controllers\Jabatan;
use Modules\Data\Controllers\Unit;

//----------------------------------Section Data-------------------------------------

//----------------------------------Data Anggota-------------------------------------
$routes->group('data/anggota', static function ($routes) {
    $routes->add('get', [Anggota::class, 'get']);
    $routes->add('store', [Anggota::class, 'store'], [ 'filter' => 'api-validation:mu_anggota']);
});

$routes->group('data', [
    'filter' => 'api-auth:admin',
], static function ($routes) {
//----------------------------------Data PCM-------------------------------------
    $routes->group('pcm', static function ($routes) {
        $routes->add('/', [Pcm::class, 'index']);
        $routes->add('prodi', [Pcm::class, 'prodi']);
        $routes->add('get', [Pcm::class, 'get']);
        $routes->add('store', [Pcm::class, 'store'], [ 'filter' => 'api-validation:mu_pcm']);
        $routes->add('delete/(:any)', [Pcm::class, 'delete/$1']);
        $routes->add('delete_many', [Pcm::class, 'delete_many']);
        $routes->add('template', [Pcm::class, 'template']);
        $routes->add('options', [Pcm::class, 'options']);
        $routes->add('upload', [Pcm::class, 'upload']);
    });
    
//----------------------------------Data Anggota-------------------------------------
    $routes->group('anggota', static function ($routes) {
        $routes->add('/', [Anggota::class, 'index']);
        $routes->add('prodi', [Anggota::class, 'prodi']);
        $routes->add('delete/(:any)', [Anggota::class, 'delete/$1']);
        $routes->add('delete_many', [Anggota::class, 'delete_many']);
        $routes->add('template', [Anggota::class, 'template']);
        $routes->add('upload', [Anggota::class, 'upload']);
        $routes->add('kelas', [Anggota::class, 'kelas']);
        $routes->add('options', [Anggota::class, 'options']);
        $routes->add('search', [Anggota::class, 'search']);
    });
    
//----------------------------------Data Prm-------------------------------------
    $routes->group('prm', static function ($routes) {
        $routes->add('/', [Prm::class, 'index']);
        $routes->add('prodi', [Prm::class, 'prodi']);
        $routes->add('get', [Prm::class, 'get']);
        $routes->add('reset_options', [Prm::class, 'resetOptions']);
        $routes->add('store', [Prm::class, 'store'], [ 'filter' => 'api-validation:mu_prm']);
        $routes->add('delete/(:any)', [Prm::class, 'delete/$1']);
        $routes->add('delete_many', [Prm::class, 'delete_many']);
        $routes->add('template', [Prm::class, 'template']);
        $routes->add('upload', [Prm::class, 'upload']);
        $routes->add('options', [Prm::class, 'options']);
    });

//----------------------------------Data Jabatan-------------------------------------
$routes->group('jabatan', static function ($routes) {
    $routes->add('/', [Jabatan::class, 'index']);
    $routes->add('prodi', [Jabatan::class, 'prodi']);
    $routes->add('get', [Jabatan::class, 'get']);
    $routes->add('reset_options', [Jabatan::class, 'resetOptions']);
    $routes->add('store', [Jabatan::class, 'store'], [ 'filter' => 'api-validation:mu_jabatan']);
    $routes->add('delete/(:any)', [Jabatan::class, 'delete/$1']);
    $routes->add('delete_many', [Jabatan::class, 'delete_many']);
    $routes->add('template', [Jabatan::class, 'template']);
    $routes->add('upload', [Jabatan::class, 'upload']);
    $routes->add('options', [Jabatan::class, 'options']);
});


//----------------------------------Data Unit-------------------------------------
$routes->group('unit', static function ($routes) {
    $routes->add('/', [Unit::class, 'index']);
    $routes->add('prodi', [Unit::class, 'prodi']);
    $routes->add('get', [Unit::class, 'get']);
    $routes->add('reset_options', [Unit::class, 'resetOptions']);
    $routes->add('store', [Unit::class, 'store'], [ 'filter' => 'api-validation:mu_unit']);
    $routes->add('delete/(:any)', [Unit::class, 'delete/$1']);
    $routes->add('delete_many', [Unit::class, 'delete_many']);
    $routes->add('template', [Unit::class, 'template']);
    $routes->add('upload', [Unit::class, 'upload']);
    $routes->add('options', [Unit::class, 'options']);
});

//----------------------------------Data Iqab-------------------------------------
    $routes->group('iqab', static function ($routes) {
        $routes->add('/', [Iqab::class, 'index']);
        $routes->add('prodi', [Iqab::class, 'prodi']);
        $routes->add('get', [Iqab::class, 'get']);
        $routes->add('reset_options', [Iqab::class, 'resetOptions']);
        $routes->add('store', [Iqab::class, 'store'], [ 'filter' => 'api-validation:daiq_iqab']);
        $routes->add('delete/(:any)', [Iqab::class, 'delete/$1']);
        $routes->add('delete_many', [Iqab::class, 'delete_many']);
        $routes->add('template', [Iqab::class, 'template']);
        $routes->add('upload', [Iqab::class, 'upload']);
        $routes->add('options', [Iqab::class, 'options']);
    });

//----------------------------------Data Prm Iqab-------------------------------------
    $routes->group('pelanggaran_iqab', static function ($routes) {
        $routes->add('/', [PrmIqab::class, 'index']);
        $routes->add('prodi', [PrmIqab::class, 'prodi']);
        $routes->add('get', [PrmIqab::class, 'get']);
        $routes->add('reset_options', [PrmIqab::class, 'resetOptions']);
        $routes->add('store', [PrmIqab::class, 'store'], [ 'filter' => 'api-validation:daiq_pelanggaran_iqab']);
        $routes->add('delete/(:any)', [PrmIqab::class, 'delete/$1']);
        $routes->add('delete_many', [PrmIqab::class, 'delete_many']);
        $routes->add('template', [PrmIqab::class, 'template']);
        $routes->add('upload', [PrmIqab::class, 'upload']);
        $routes->add('options', [PrmIqab::class, 'options']);
    });
});