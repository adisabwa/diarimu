<?php

use Modules\Data\Controllers\GroupController as Group;
use Modules\Data\Controllers\GroupActivityController as GroupActivity;
use Modules\Data\Controllers\Prm;
use Modules\Data\Controllers\Anggota;
use Modules\Data\Controllers\Iqab;
use Modules\Data\Controllers\SholatSunnahController as SholatSunnah;

//----------------------------------Section Data-------------------------------------

//----------------------------------Data Sholat Sunnah-------------------------------------
$routes->group('data/sholat-sunnah', static function ($routes) {
    $routes->add('/', [SholatSunnah::class, 'index']);
    $routes->add('get', [SholatSunnah::class, 'get']);
    $routes->add('reset_options', [SholatSunnah::class, 'resetOptions']);
    $routes->add('store', [SholatSunnah::class, 'store'], [ 'filter' => 'api-validation:mu__sholat_sunnah']);
    $routes->add('delete/(:any)', [SholatSunnah::class, 'delete/$1']);
    $routes->add('delete_many', [SholatSunnah::class, 'delete_many']);
    $routes->add('template', [SholatSunnah::class, 'template']);
    $routes->add('upload', [SholatSunnah::class, 'upload']);
    $routes->add('options', [SholatSunnah::class, 'options']);
});


//----------------------------------Data Anggota-------------------------------------
$routes->group('data/anggota', static function ($routes) {
    $routes->add('get', [Anggota::class, 'get']);
    $routes->add('get_where', [Anggota::class, 'get_where']);
    $routes->add('store', [Anggota::class, 'store'], [ 'filter' => 'api-validation:mu_anggota']);
});

$routes->group('data', [
    'filter' => 'api-auth',
], static function ($routes) {
//----------------------------------Data Group-------------------------------------
    $routes->group('group', static function ($routes) {
        $routes->add('/', [Group::class, 'index']);
        $routes->add('get', [Group::class, 'get']);
        $routes->add('store', [Group::class, 'store'], [ 'filter' => 'api-validation:mu_group']);
        $routes->add('get_anggota', [Group::class, 'get_anggota']);
        $routes->add('delete/(:any)', [Group::class, 'delete/$1']);
        $routes->add('delete_many', [Group::class, 'delete_many']);
        $routes->add('template', [Group::class, 'template']);
        $routes->add('options', [Group::class, 'options']);
        $routes->add('upload', [Group::class, 'upload']);
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

//----------------------------------Data Group Activity-------------------------------------
$routes->group('group/activity', static function ($routes) {
    $routes->add('/', [GroupActivity::class, 'index']);
    $routes->add('prodi', [GroupActivity::class, 'prodi']);
    $routes->add('get', [GroupActivity::class, 'get']);
    $routes->add('reset_options', [GroupActivity::class, 'resetOptions']);
    $routes->add('store', [GroupActivity::class, 'store'], [ 'filter' => 'api-validation:mu_group_activity']);
    $routes->add('delete/(:any)', [GroupActivity::class, 'delete/$1']);
    $routes->add('delete_many', [GroupActivity::class, 'delete_many']);
    $routes->add('options', [GroupActivity::class, 'options']);
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