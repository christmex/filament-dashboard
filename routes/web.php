<?php

use App\Helpers\Helper;
use App\Models\Classroom;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

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
    dd(Role::whereIn('id',[1,2,3])->where('name',Helper::$userDependOnRoleMainTeacher)->get()->count());
    // dd(auth()->user()->canImpersonate());
});