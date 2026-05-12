<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('login');
// });

use App\Http\Controllers\LoginController;
use App\Http\Controllers\admin\DashboardController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/change_password', [LoginController::class, 'changePassword'])->name('change-password');
Route::post('/update-password', [LoginController::class, 'updatePassword'])->name('password.update');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');



// Dashboard
// Route::get('/dashboard', function () {
//     return view('index');   // your index.blade.php dashboard
// })->middleware('adminAuth')->name('dashboard');



use App\Http\Controllers\admin\UnitController;

Route::prefix('admin')->group(function () {

    Route::get('add-unit', [UnitController::class, 'add_unit'])->name('add-unit');
    Route::get('manage-units', [UnitController::class, 'manage_unit'])->name('manage-units');

    Route::post('store-units', [UnitController::class, 'store_units'])->name('store-units');

    Route::get('units-list', [UnitController::class, 'get_units'])->name('units-list'); // AJAX

    Route::get('edit-unit/{id}', [UnitController::class, 'edit_unit'])->name('edit-unit');
    Route::post('update-unit/{id}', [UnitController::class, 'update_unit'])->name('update-unit');

    Route::get('delete-unit/{id}', [UnitController::class, 'delete_unit'])->name('delete-unit');
});





use App\Http\Controllers\admin\TestController;

Route::prefix('admin')->group(function () {

    Route::get('add_test', [TestController::class, 'add_test'])->name('add-test');
    Route::get('manage_tests', [TestController::class, 'manage_test'])->name('manage-tests');

    Route::get('ajax-tests', [TestController::class, 'ajax_tests'])->name('ajax-tests');


    Route::post('store-test', [TestController::class, 'store_test'])->name('store-test');

    Route::get('edit_test/{id}', [TestController::class, 'edit_test'])->name('edit-test');
    Route::post('update-test/{id}', [TestController::class, 'update_test'])->name('update-test');

    Route::get('delete-test/{id}', [TestController::class, 'delete_test'])->name('delete-test');
});


use App\Http\Controllers\admin\CompanyController;

Route::prefix('admin')->group(function () {

    Route::get('add_company', [CompanyController::class, 'add_company'])->name('add-company');
    Route::get('manage_companies', [CompanyController::class, 'manage_company'])->name('manage-company');
    Route::get('ajax-companies', [CompanyController::class, 'ajax_companies'])->name('ajax-companies');
    Route::post('store-company', [CompanyController::class, 'store_company'])->name('store-company');
    Route::get('edit-company/{id}', [CompanyController::class, 'edit_company'])->name('edit-company');
    Route::post('update-company/{id}', [CompanyController::class, 'update_company'])->name('update-company');
    Route::get('delete-company/{id}', [CompanyController::class, 'delete_company'])->name('delete-company');


});


use App\Http\Controllers\admin\LocationController;

Route::prefix('admin')->group(function () {

    Route::get('add_location', [LocationController::class, 'add_location'])->name('add-location');
    Route::post('store-location', [LocationController::class, 'store_location'])->name('store-location');

    Route::get('ajax_locations', [LocationController::class, 'ajax_locations'])->name('ajax-locations');

    Route::get('manage_locations', [LocationController::class, 'manage_location'])->name('manage-locations');
    Route::get('locations-list', [LocationController::class, 'get_locations'])->name('locations-list');

    Route::get('edit-location/{id}', [LocationController::class, 'edit_location'])->name('edit-location');
    Route::post('update-location/{id}', [LocationController::class, 'update_location'])->name('update-location');

    Route::get('delete-location/{id}', [LocationController::class, 'delete_location'])->name('delete-location');

});



use App\Http\Controllers\admin\BranchController;

Route::prefix('admin')->group(function () {

    Route::get('add_branch', [BranchController::class, 'add_branch'])->name('add-branch');
    Route::post('store_branch', [BranchController::class, 'store_branch'])->name('store-branch');
    Route::get('ajax_branches', [BranchController::class, 'ajax_branches'])->name('ajax-branches');
    Route::get('manage_branch', [BranchController::class, 'manage_branch'])->name('manage-branch');
    Route::get('edit-branch/{id}', [BranchController::class, 'edit_branch'])->name('edit-branch');
    Route::post('update-branch/{id}', [BranchController::class, 'update_branch'])->name('update-branch');
    Route::get('delete-branch/{id}', [BranchController::class, 'delete_branch'])->name('delete-branch');


});



use App\Http\Controllers\admin\OpController;

Route::prefix('admin')->group(function () {

    Route::get('add_op', [OpController::class, 'add_op'])->name('add-op');
    Route::get('fetch_patient', [OpController::class, 'fetch_patient'])->name('fetch-patient');
    Route::post('store_op', [OpController::class, 'store_op'])->name('store-op');
    Route::get('ajax_op', [OpController::class, 'ajax_op'])->name('ajax-op');
    Route::get('manage_op', [OpController::class, 'manage_op'])->name('manage-op');
    Route::get('edit-op/{id}', [OpController::class, 'edit_op'])->name('edit-op');
    Route::put('update-op/{id}', [OpController::class, 'update_op'])->name('update-op');

});


use App\Http\Controllers\admin\ResultController;

Route::prefix('admin')->group(function () {

    Route::get('add_result', [ResultController::class, 'add_result'])->name('add-result');
    Route::get('manage_result', [ResultController::class, 'manage_result'])->name('manage-result');
    // Route::get('view_result', [ResultController::class, 'view_result'])->name('view-result');
    Route::get('view_result/{id}', [ResultController::class, 'view_result'])->name('view-result');
    Route::get('ajax_results', [ResultController::class, 'ajax_results'])->name('ajax-results');
    Route::get('fetch_patient_result', [ResultController::class, 'fetch_patient_result'])->name('fetch-patient');
    Route::get('fetch_tests', [ResultController::class, 'fetch_tests'])->name('fetch-tests');
    Route::post('store_result', [ResultController::class, 'store_result'])->name('store-result');
    Route::get('export_results', [ResultController::class, 'export_results'])->name('export-results');
    Route::get('download_result/{id}', [ResultController::class, 'download_result'])->name('download-result');


});

use App\Http\Controllers\admin\TeamController;

Route::prefix('admin')->group(function () {

    Route::get('add_team', [TeamController::class, 'add_team'])->name('add-team');
    Route::post('store_team', [TeamController::class, 'store_team'])->name('team.store');
    Route::get('manage_team', [TeamController::class, 'manage_team'])->name('manage-team');

    Route::get('/team_list_ajax', [TeamController::class, 'team_list_ajax'])->name('team.list.ajax');
    Route::get('/edit_team/{id}', [TeamController::class, 'edit_team'])->name('team.edit');
    Route::post('/update_team/{id}', [TeamController::class, 'update_team'])->name('team.update');
    Route::delete('/delete_team/{id}', [TeamController::class, 'delete_team'])->name('team.delete');
});

