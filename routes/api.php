<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MasyarakatController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\TanggapanController;
use App\Http\Controllers\KategoriController;

Route::post('login', [LoginController::class,'login']);
Route::post('registerMasyarakat', [LoginController::class,'registerMasyarakat']);
Route::post('registerPetugas', [LoginController::class,'registerPetugas']);

Route::group(['middleware' => ['jwt.verify:admin,petugas,masyarakat']], function () {
    Route::get('login/check', [LoginController::class,'loginCheck']);
    Route::post('logout', [LoginController::class,'logout']);   
});

// API ADMIN
Route::group(['middleware' => ['jwt.verify:admin']], function () { //untuk hak akses admin
    //API MASYARAKAT
	Route::get('masyarakat', [MasyarakatController::class, 'getAll']);
	Route::get('masyarakat/{id}', [MasyarakatController::class, 'getId']);
	Route::get('masyarakat/{limit}/{offset}', [MasyarakatController::class, 'getAll']);
    Route::post('masyarakat', [MasyarakatController::class, 'insert']);
    Route::put('masyarakat/{id_user}', [MasyarakatController::class, 'update']);
    Route::delete('masyarakat/{id_user}', [MasyarakatController::class, 'delete']);

    //API PETUGAS
	Route::get('petugas', [PetugasController::class, 'getAll']);
	Route::get('petugas/{id}', [PetugasController::class, 'getId']);
	Route::get('petugas/{limit}/{offset}', [PetugasController::class, 'getAll']);
    Route::post('petugas', [PetugasController::class, 'insert']);
    Route::put('petugas/{id_user}', [PetugasController::class, 'update']);
    Route::delete('petugas/{id_user}', [PetugasController::class, 'delete']);

    //API KATEGORI PENGADUAN
	Route::get('kategori/{id_kategori}', [KategoriController::class, 'getId']);
	Route::get('kategori/{limit}/{offset}', [KategoriController::class, 'getAll']);
    Route::post('kategori', [KategoriController::class, 'insert']);
    Route::put('kategori/{id_user}', [KategoriController::class, 'update']);
    Route::delete('kategori/{id_user}', [KategoriController::class, 'delete']);
});

Route::group(['middleware' => ['jwt.verify:admin,petugas']], function () {
    //API MASYARAKAT
    Route::get('masyarakat', [MasyarakatController::class, 'getAll']);
	Route::get('masyarakat/{id}', [MasyarakatController::class, 'getId']);
	Route::get('masyarakat/{limit}/{offset}', [MasyarakatController::class, 'getAll']);
    Route::put('masyarakat/{id_user}', [MasyarakatController::class, 'update']);
    Route::delete('masyarakat/{id_user}', [MasyarakatController::class, 'delete']);
    
    //API PENGADUAN
	Route::get('pengaduan', [PengaduanController::class, 'getAllPengaduan']);
	Route::get('pengaduan/{id_pengaduan}', [PengaduanController::class, 'getId']);
	Route::get('pengaduan/{limit}/{offset}', [PengaduanController::class, 'getAllPengaduan']);
	Route::post('pengaduan/status', [PengaduanController::class, 'changeStatus']);
	Route::post('pengaduan/tanggapan', [TanggapanController::class, 'send']); //input tanggapan
    
});

//API MASYARAKAT AKSES
Route::group(['middleware' => ['jwt.verify:masyarakat']], function () { //untuk hak akses masyarakat
    //PENGADUAN
    // Route::get('masyarakat/pengaduan' => )
	Route::get('masyarakat/pengaduan/{limit}/{offset}', [PengaduanController::class, 'getAllPengaduan']); //get all
	Route::get('detail/{id_pengaduan}', [PengaduanController::class, 'getId']);
	Route::post('masyarakat/pengaduan', [PengaduanController::class, 'insert']);
	Route::delete('masyarakat/pengaduan/{id_pengaduan}', [PengaduanController::class, 'destroy']);
    
    // KATEGORI
    Route::get('kategori', [KategoriController::class, 'getAll']); 
});