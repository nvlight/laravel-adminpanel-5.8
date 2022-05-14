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

use \App\Http\Controllers\Blog\Admin\OrderController;
use \App\Http\Controllers\Blog\Admin\CategoryController;
use \App\Http\Controllers\Blog\Admin\ProductController;
use \App\Http\Controllers\Blog\Admin\FilterController;
use \App\Http\Controllers\Blog\Admin\CurrencyController;

Route::get('/', function () {
    return view('welcome');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(
  ['middleware' => ['status', 'auth'],], function () {


    $groupData = [
        'namespace' => 'Blog\Admin',
        'prefix' => 'admin',
    ];

    Route::group($groupData, function () {
        Route::resource('index', 'MainController')
            ->names('blog.admin');

        Route::resource('orders', 'OrderController')
            ->names('blog.admin.orders');
        Route::get('/orders/change/{id}', [OrderController::class,'change'])
            ->name('blog.admin.orders.change');
        Route::post('/orders/save/{id}',[OrderController::class,'save'])
            ->name('blog.admin.orders.save');
        Route::get('/orders/forcedestroy/{id}',[OrderController::class,'forcedestroy'])
            ->name('blog.admin.orders.forcedestroy');

        Route::get('/categories/mydel', [CategoryController::class, 'mydel'])
            ->name('blog.admin.categories.mydel');
        Route::resource('categories', 'CategoryController')
            ->names('blog.admin.categories');

        Route::resource('users', 'UserController')
            ->names('blog.admin.users');

        Route::get('/products/related', [ProductController::class, 'related']);

        Route::match(['get','post'],'/products/ajax-image-upload',[ProductController::class,'ajaxImage']);

        Route::delete('/products/ajax-image-remove/{filename}',  [ProductController::class,'deleteImage']); //'ProductController@deleteImage'
        //Route::get('/products/ajax-image-remove/{filename}',[ProductController::class,'deleteImage']);

        Route::post('/products/gallery',[ProductController::class,'gallery'])
            ->name('blog.admin.products.gallery');
        Route::post('/products/delete-gallery',[ProductController::class,'deleteGallery'])
            ->name('blog.admin.products.deletegallery');

        Route::get('/products/testpush', function (){
            echo "its all fine <br>";
            //dump(\Session::get('chich'));
            dump(\Session::all());
//            \Session::push('chich', 'first chich');
//            \Session::push('chich', 'second chich');
//            \Session::push('chich', 'fird chich');
//            \Session::push('chich', 'and so on chich!');
        });

//        Route::get('/products/return-status/{id}', [ProductController::class, 'returnStatus'] )
//            ->name('blog.admin.products.returnstatus');
//        Route::get('/products/delete-status/{id}', [ProductController::class, 'deleteStatus'] )
//            ->name('blog.admin.products.deletestatus');
        Route::get('/products/change-status/{product}/{status}', [ProductController::class, 'changeStatus'] )
            ->name('blog.admin.products.changestatus');

        Route::get('/products/delete-product/{product}', [ProductController::class, 'deleteProduct'] )
            ->name('blog.admin.products.deleteproduct');

        Route::get('/filter/group-filter', [FilterController::class, 'attributeGroup'])
            ->name('blog.admin.filter-group');
        Route::match(['get','post'], '/filter/group-add-group',  [FilterController::class, 'groupAdd']);
        Route::match(['get','post'], '/filter/group-edit/{attributeGroup}',  [FilterController::class, 'groupEdit']);
        Route::get( '/filter/group-delete/{attributeGroup}',[FilterController::class, 'groupDelete']);

        Route::match(['get','post'], '/filter/attrs-add/{attributeValue?}',  [FilterController::class, 'attributeAdd'])
            ->name('blog.admin.filter.attribute-add');
        Route::match(['get','post'], '/filter/attrs-edit/{attributeValue}', [FilterController::class, 'attributeEdit'])
            ->name('blog.admin.filter.attribute-edit');
        Route::get( '/filter/attrs-delete/{attributeValue}',                [FilterController::class, 'attributeDelete'])
            ->name('blog.admin.filter-attribute-delete');
        Route::get('/filter/attributes-filter', [FilterController::class, 'attributeFilter'])
            ->name('blog.admin.filter.attribute');

        Route::get('/currency/index', [CurrencyController::class,'index'])
            ->name('blog.admin.currency');
        // url('/admin/currency/edit
        Route::match(['get','post'], '/currency/add/',  [CurrencyController::class, 'add'])
            ->name('blog.admin.currency-add');
        Route::match(['get','post'], '/currency/edit/{currency?}', [CurrencyController::class, 'edit'])
            ->name('blog.admin.currency-edit');
        Route::get( '/currency/delete/{currency}',                [CurrencyController::class, 'delete'])
            ->name('blog.admin.currency-delete');
        // url('/admin/currency/delete

        //Route::get('/search/result', 'SearchController');
        //Route::get('/autocomplete', '');

        Route::resource('products', 'ProductController')
            ->names('blog.admin.products');
    });

  }
);
// add comments
Route::get('user/index', 'Blog\User\MainController@index');

Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')
    ->name('ckfinder_connector');

Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')
    ->name('ckfinder_browser');

//Route::any('/ckfinder/examples/{example?}', 'CKSource\CKFinderBridge\Controller\CKFinderController@examplesAction')
//    ->name('ckfinder_examples');
