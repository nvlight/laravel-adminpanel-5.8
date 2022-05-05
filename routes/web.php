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
