<?php

use Illuminate\Support\Facades\Route;


Route::namespace('Api')->name('api.')->middleware(['api'])->group(function () {
    Route::get('general-setting', 'BasicController@generalSetting'); //ok
    Route::get('unauthenticate', 'BasicController@unauthenticate')->name('unauthenticate'); //ok
    Route::get('languages', 'BasicController@languages'); //ok
    Route::get('language-data/{code}', 'BasicController@languageData'); //ok
    Route::get('countries', 'BasicController@countries'); //ok

    Route::get('categories', 'CategoryController@categories'); //ok
    Route::get('products', 'ProductController@products')->name('products.index'); //ok
    Route::get('products/{product}', 'ProductController@product'); //ok
    Route::get('merchants', 'UserController@merchants')->name('merchants.index'); //ok
    Route::get('merchant/profile', 'UserController@merchantProfile')->name('merchant.profile'); //ok
    Route::get('posts', 'PostController@posts')->name('posts.index'); //ok
    Route::get('posts/{id}', 'PostController@post')->name('posts.show'); //ok
    Route::get('home', 'HomeController@home')->name('home.index'); //ok

    Route::namespace('Auth')->group(function () {
        Route::post('login', 'LoginController@login'); //ok
        Route::post('register', 'RegisterController@register'); //ok

        Route::post('password/email', 'ForgotPasswordController@sendResetCodeEmail');
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode');

        Route::post('password/reset', 'ResetPasswordController@reset');
    });


    Route::middleware(['auth.api:api'])->group(function () {
        Route::post('product/bid', 'ProductController@bid');
        Route::post('product/save-review', 'ProductController@saveProductReview');
        Route::post('merchant/save-review', 'ProductController@saveMerchantReview');

        Route::prefix('user')->name('user')->group(function () {
            Route::get('logout', 'Auth\LoginController@logout');
            Route::get('authorization', 'AuthorizationController@authorization')->name('authorization');
            Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
            Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
            Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
            Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

            Route::middleware(['checkStatusApi'])->group(function () {
                Route::get('dashboard', 'UserController@dashboard');

                Route::post('profile-setting', 'UserController@submitProfile');
                Route::post('change-password', 'UserController@submitPassword');

                // Withdraw
                Route::get('withdraw/methods', 'UserController@withdrawMethods');
                Route::post('withdraw/store', 'UserController@withdrawStore');
                Route::post('withdraw/confirm', 'UserController@withdrawConfirm');
                Route::get('withdraw/history', 'UserController@withdrawLog');


                // Deposit
                Route::get('deposit/methods', 'PaymentController@depositMethods');
                Route::post('deposit/insert', 'PaymentController@depositInsert');
                Route::get('deposit/confirm', 'PaymentController@depositConfirm');

                Route::get('deposit/manual', 'PaymentController@manualDepositConfirm');
                Route::post('deposit/manual', 'PaymentController@manualDepositUpdate');

                Route::get('deposit/history', 'UserController@depositHistory');

                Route::get('transactions', 'UserController@transactions');

            });
        });
    });
});