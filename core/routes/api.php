<?php

use Illuminate\Support\Facades\Route;


Route::namespace('Api')->name('api.')->middleware(['api'])->group(function () {
    Route::get('general-setting', 'BasicController@generalSetting'); //ok
    Route::get('unauthenticate', 'BasicController@unauthenticate')->name('unauthenticate'); //ok
    Route::get('merchant-unauthenticate', 'BasicController@merchantUnauthenticate')->name('merchant-unauthenticate'); //ok
    Route::get('languages', 'BasicController@languages'); //ok
    Route::get('language-data/{code}', 'BasicController@languageData'); //ok
    Route::get('countries', 'BasicController@countries'); //ok

    Route::get('categories', 'CategoryController@categories'); //ok
    Route::get('products', 'ProductController@products')->name('products.index'); //ok
    Route::get('products/{product}', 'ProductController@product')->name('products.show'); //ok
    Route::get('merchants', 'UserController@merchants')->name('merchants.index'); //ok
    Route::get('merchant/profile', 'UserController@merchantProfile')->name('merchant.profile'); //ok
    Route::get('posts', 'PostController@posts')->name('posts.index'); //ok
    Route::get('posts/{id}', 'PostController@post')->name('posts.show'); //ok
    Route::get('home', 'HomeController@home')->name('home.index'); //ok
    Route::post('submit-contact', 'BasicController@submitContact')->name('contact.store'); //ok
    Route::get('terms-conditions', 'BasicController@termsConditions')->name('terms-conditions'); //ok
    Route::get('privacy-policy', 'BasicController@privacyPolicy')->name('privacy-policy'); //ok
    Route::get('contact-content', 'BasicController@contactContent')->name('contact-content'); //ok
    Route::get('about-content', 'BasicController@aboutContent')->name('about-content'); //ok

    Route::namespace('Auth')->group(function () {
        Route::post('login', 'LoginController@login'); //ok
        Route::post('register', 'RegisterController@register'); //ok
        Route::post('password/email', 'ForgotPasswordController@sendResetCodeEmail'); //ok
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode'); //ok
        Route::post('password/reset', 'ResetPasswordController@reset'); //ok

        Route::post('merchant/login', 'LoginController@merchantLogin'); //ok
        Route::post('merchant/register', 'RegisterController@merchantRegister'); //ok
        Route::post('merchant/password/email', 'ForgotPasswordController@merchantSendResetCodeEmail'); //ok
        Route::post('merchant/password/reset', 'ResetPasswordController@merchantReset'); //ok
    });

    Route::middleware(['auth.api_merchant:api_merchant'])->group(function () {
        Route::prefix('merchant')->name('merchant.')->group(function () {
            Route::get('logout', 'Auth\LoginController@merchantLogout'); //ok
            Route::get('authorization', 'AuthorizationController@merchantAuthorization')->name('authorization'); //ok
            Route::middleware(['checkStatusMerchantApi'])->group(function () {
                Route::get('dashboard', 'MerchantController@dashboard'); //ok
                Route::get('products', 'MerchantController@products'); //ok
                Route::post('store-product', 'MerchantController@storeProduct'); //ok
                Route::post('update-product/{id}', 'MerchantController@updateProduct'); //ok
                Route::get('product/{id}/bids', 'MerchantController@productBids'); //ok
                Route::post('bid/winner', 'MerchantController@bidWinner'); //ok
                Route::get('product/winners', 'MerchantController@productWinner'); //ok
                Route::post('product/delivered', 'MerchantController@deliveredProduct'); //ok
                Route::get('transactions', 'MerchantController@transactions'); //ok

                Route::post('profile-setting', 'MerchantController@submitProfile'); //ok
                Route::post('change-password', 'MerchantController@submitPassword'); //ok
            });
        });
    });

    Route::middleware(['auth.api:api'])->group(function () {
        Route::post('product/bid', 'ProductController@bid'); //ok
        Route::post('product/save-review', 'ProductController@saveProductReview'); //ok
        Route::post('merchant/save-review', 'ProductController@saveMerchantReview'); //ok

        Route::prefix('user')->name('user')->group(function () {
            Route::get('logout', 'Auth\LoginController@logout'); //ok
            Route::get('authorization', 'AuthorizationController@authorization')->name('authorization'); //ok
            Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
            Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
            Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
            Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

            Route::middleware(['checkStatusApi'])->group(function () {
                Route::get('dashboard', 'UserController@dashboard'); //ok
                Route::get('bidding-history', 'UserController@biddingHistory'); //ok
                Route::get('winning-history', 'UserController@winningHistory'); //ok

                //Ticket
                Route::get('ticket', 'UserController@ticket'); //ok
                Route::get('ticket/view/{id}', 'UserController@viewTicket'); //ok
                Route::get('ticket/close/{id}', 'UserController@closeTicket'); //ok
                Route::post('ticket/store', 'UserController@storeTicket'); //ok
                Route::post('ticket/reply/{id}', 'UserController@replyTicket'); //ok

                Route::get('profile-details', 'UserController@profile'); //ok
                Route::post('profile-setting', 'UserController@submitProfile'); //ok
                Route::post('change-password', 'UserController@submitPassword'); //ok

                // Withdraw
                Route::get('withdraw/methods', 'UserController@withdrawMethods');
                Route::post('withdraw/store', 'UserController@withdrawStore');
                Route::post('withdraw/confirm', 'UserController@withdrawConfirm');
                Route::get('withdraw/history', 'UserController@withdrawLog');


                // Deposit
                Route::get('deposit/methods', 'PaymentController@depositMethods'); //ok
                Route::post('deposit/insert', 'PaymentController@depositInsert'); //ok
                Route::get('deposit/confirm', 'PaymentController@depositConfirm'); //ok

                Route::get('deposit/manual', 'PaymentController@manualDepositConfirm'); //ok
                Route::post('deposit/manual', 'PaymentController@manualDepositUpdate'); //ok

                Route::get('deposit/history', 'UserController@depositHistory'); //ok

                Route::get('transactions', 'UserController@transactions'); //ok

            });
        });
    });
});