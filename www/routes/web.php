<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();


if ($_SERVER['HTTP_HOST'] == "youcandojewelry.com" ) {
    URL::forceScheme('https');
}

Route::get('/', function () {

    if (Auth::check()) {
        return redirect()->route('bill');
    } else {
        return view('auth/login');
    }

});

Route::get('/checkHasCookiePin', 'Auth\LoginController@checkHasPin');

Route::group(['middleware' => 'checkPin'], function () {

Route::get('/bill', 'HomeController@index')->name('bill');
Route::post('/bill', 'HomeController@initBill');
Route::post('/bill/delete', 'HomeController@voidBill');
Route::get('/bill/update', 'HomeController@index');
Route::post('/bill/update/deliver', 'HomeController@updateBillDeliver');
Route::post('/bill/uploadimg','HomeController@uploadImg');
Route::post('/bill/deleteImg','HomeController@deleteImg');
Route::post('/bill/payment/delete','HomeController@voidPayment');

Route::get('/report','Report\ReportController@index');
Route::post('/report','Report\ReportController@getReport');

Route::get('/summary','Report\SummaryController@index');
Route::post('/summary','Report\SummaryController@getSummary');

Route::get('/recent', 'Recent\RecentController@index');
Route::post('/recent', 'Recent\RecentController@getBillBydate');
Route::get('/recent/bill', 'HomeController@print');


Route::get('/api/bill', 'Recent\RecentController@getBill');

Route::get('/customer', 'Customer\CustomerListController@index')->name('customer');
Route::get('/customer/create', 'Customer\CustomerListController@pageCreate');
Route::post('/customer/create', 'Customer\CustomerListController@create');
Route::post('/customer/createWell', 'Customer\CustomerListController@createWell');
Route::get('/customer/update', 'Customer\CustomerListController@pageUpdate');
Route::post('/customer/update', 'Customer\CustomerListController@update');
Route::get('/customer/delete', 'Customer\CustomerListController@delete');
Route::get('/customer/search', 'Customer\CustomerListController@search');
Route::get('/customer/searchWell', 'Customer\CustomerListController@searchWell');

Route::group(['middleware' => 'role:admin'], function () {

    Route::get('/register', 'Auth\RegisterController@index')->name('register');
    Route::get('/user', 'Auth\UserListController@index')->name('user-list');
    Route::get('/user/update', 'Auth\UpdateController@index')->name('update-user');
    Route::post('/user/update', 'Auth\UpdateController@update')->name('update-update-user');
    Route::post('/user/password', 'Auth\UpdateController@resetPassword')->name('update-reset-password');
    Route::get('/user/delete', 'Auth\UpdateController@delete')->name('update-delete-user');
    Route::post('/user/setPin', 'Auth\UpdateController@setPin');

    Route::get('/branch', 'Branch\BranchController@index')->name('branch');
    Route::get('/branch/create', 'Branch\BranchController@pageCreate');
    Route::post('/branch/create', 'Branch\BranchController@create');
    Route::get('/branch/update', 'Branch\BranchController@pageUpdate');
    Route::post('/branch/update', 'Branch\BranchController@update');
    Route::get('/branch/delete', 'Branch\BranchController@delete');
    Route::get('/branch/craft', 'Branch\BranchController@craftPage');
    Route::post('/branch/craft', 'Branch\BranchController@craftCreate');
    Route::get('/branch/craft/delete', 'Branch\BranchController@craftDelete');

    Route::get('/material', 'Material\MaterialController@index')->name('material');
    Route::post('/material/order', 'Material\MaterialController@order');
    Route::post('/material/update', 'Material\MaterialController@updateMaterial');
    Route::post('/material/addMaterial', 'Material\MaterialController@addMaterial');
    Route::get('/material/delete', 'Material\MaterialController@deleteMaterial');

    Route::get('/setting', 'Setting\SettingController@index')->name('setting');
    Route::post('/setting/update', 'Setting\SettingController@updateSetting');

    Route::post('api/setting/order', 'Setting\SettingController@order');
    Route::post('api/setting/addJob', 'Setting\SettingController@addJob');
    Route::post('api/setting/addAmulet', 'Setting\SettingController@addAmulet');
    Route::post('api/setting/updateJob', 'Setting\SettingController@updateJob');
    Route::post('api/setting/updateAmulet', 'Setting\SettingController@updateAmulet');


    Route::get('/export', 'Export\ExportController@index')->name('export');
    Route::post('api/export', 'Export\ExportController@export');
    Route::post('api/export/getLength', 'Export\ExportController@checkLength');
    Route::post('api/export/dropLength', 'Export\ExportController@dropLenght');

    Route::get('export/dump/{file_name}', function($file_name = null)
    {
        $path = storage_path().'/'.'backup/' .$file_name;
        if (file_exists($path)) {
            return Response::download($path);
        }
    });



});//role:admin
});//router web


