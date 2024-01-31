<?php

use App\Helpers\Helper;
use App\Models\Classroom;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/debug',function(){
    dd(Classroom::all()->pluck('name','id')->toArray());
    // dd(auth()->user()->canImpersonate());
});