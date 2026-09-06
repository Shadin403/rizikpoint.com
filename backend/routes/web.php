<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

use App\Http\Controllers\Auth\LoginController;

// Auth routes (excluding default login)
Auth::routes([
    'verify' => true,
    'login' => false,
]);

// Admin Auth
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Helper routes
Route::get('/refresh-csrf', function() {
    return csrf_token();
});
Route::post('/language', 'LanguageController@changeLanguage')->name('language.change');
Route::post('/currency', 'CurrencyController@changeCurrency')->name('currency.change');

// Aiz Uploader (used extensively in Admin Panel)
Route::post('/aiz-uploader', 'AizUploadController@show_uploader');
Route::post('/aiz-uploader/upload', 'AizUploadController@upload');
Route::get('/aiz-uploader/get_uploaded_files', 'AizUploadController@get_uploaded_files');
Route::post('/aiz-uploader/get_file_by_ids', 'AizUploadController@get_preview_files');
Route::get('/aiz-uploader/download/{id}', 'AizUploadController@attachment_download')->name('download_attachment');

// Cron / Demo utilities
Route::get('/demo/cron_1', 'DemoController@cron_1');
Route::get('/demo/cron_2', 'DemoController@cron_2');

// Home Landing Page (API-focused welcome page)
Route::get('/', function () {
    $frontend_url = get_setting('frontend_url');
    $current_host = request()->getHost();
    $frontend_host = $frontend_url ? parse_url($frontend_url, PHP_URL_HOST) : null;

    if (!empty($frontend_url) && 
        $current_host === $frontend_host && 
        !str_contains($current_host, 'admin') && 
        !str_contains($current_host, 'backend') && 
        !in_array($current_host, ['localhost', '127.0.0.1'])) {
        return redirect()->away($frontend_url);
    }

    if (auth()->check() && (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Placeholder product route to prevent admin views from crashing
Route::get('/product/{slug}', function ($slug) {
    $frontend_url = get_setting('frontend_url');
    if (!empty($frontend_url)) {
        return redirect()->away(rtrim($frontend_url, '/') . '/products/' . $slug);
    }
    return redirect()->route('home');
})->name('product');

// Backend Subscriber Management
Route::resource('subscribers', 'SubscriberController');

// Authenticated Backend Routes (Shared or used by Admin Panel/Sellers)
Route::group(['middleware' => ['auth']], function() {
    // Product Management
    Route::post('/products/store/', 'ProductController@store')->name('products.store');
    Route::post('/products/update/{id}', 'ProductController@update')->name('products.update');
    Route::get('/products/destroy/{id}', 'ProductController@destroy')->name('products.destroy');
    Route::get('/products/duplicate/{id}', 'ProductController@duplicate')->name('products.duplicate');
    Route::post('/products/sku_combination', 'ProductController@sku_combination')->name('products.sku_combination');
    Route::post('/products/sku_combination_edit', 'ProductController@sku_combination_edit')->name('products.sku_combination_edit');
    Route::post('/products/seller/featured', 'ProductController@updateSellerFeatured')->name('products.seller.featured');
    Route::post('/products/published', 'ProductController@updatePublished')->name('products.published');
    Route::post('/products/add-more-choice-option', 'ProductController@add_more_choice_option')->name('products.add-more-choice-option');

    // Orders & Invoices
    Route::get('invoice/{order_id}', 'InvoiceController@invoice_download')->name('invoice.download');
    Route::post('invoice/bulk-download', 'InvoiceController@bulk_invoice_download')->name('invoice.bulk_download');
    Route::resource('orders', 'OrderController');
    Route::get('/orders/destroy/{id}', 'OrderController@destroy')->name('orders.destroy');
    Route::post('/orders/details', 'OrderController@order_details')->name('orders.details');
    Route::post('/orders/update_delivery_status', 'OrderController@update_delivery_status')->name('orders.update_delivery_status');
    Route::post('/orders/update_payment_status', 'OrderController@update_payment_status')->name('orders.update_payment_status');
    Route::post('/orders/update_tracking_code', 'OrderController@update_tracking_code')->name('orders.update_tracking_code');
    Route::post('/orders/delivery-boy-assign', 'OrderController@assign_delivery_boy')->name('orders.delivery-boy-assign');

    // Reviews
    Route::resource('/reviews', 'ReviewController');

    // Withdraw Requests
    Route::resource('/withdraw_requests', 'SellerWithdrawRequestController');
    Route::get('/withdraw_requests_all', 'SellerWithdrawRequestController@request_index')->name('withdraw_requests_all');
    Route::post('/withdraw_request/payment_modal', 'SellerWithdrawRequestController@payment_modal')->name('withdraw_request.payment_modal');
    Route::post('/withdraw_request/message_modal', 'SellerWithdrawRequestController@message_modal')->name('withdraw_request.message_modal');

    // Support Conversations & Messages
    Route::resource('conversations', 'ConversationController');
    Route::get('/conversations/destroy/{id}', 'ConversationController@destroy')->name('conversations.destroy');
    Route::post('conversations/refresh', 'ConversationController@refresh')->name('conversations.refresh');
    Route::resource('messages', 'MessageController');

    // Product Bulk Upload
    Route::get('/product-bulk-upload/index', 'ProductBulkUploadController@index')->name('product_bulk_upload.index');
    Route::post('/bulk-product-upload', 'ProductBulkUploadController@bulk_upload')->name('bulk_product_upload');
    Route::get('/product-csv-download/{type}', 'ProductBulkUploadController@import_product')->name('product_csv.download');
    Route::get('/vendor-product-csv-download/{id}', 'ProductBulkUploadController@import_vendor_product')->name('import_vendor_product.download');
    Route::group(['prefix' => 'bulk-upload/download'], function() {
        Route::get('/category', 'ProductBulkUploadController@pdf_download_category')->name('pdf.download_category');
        Route::get('/brand', 'ProductBulkUploadController@pdf_download_brand')->name('pdf.download_brand');
        Route::get('/seller', 'ProductBulkUploadController@pdf_download_seller')->name('pdf.download_seller');
    });

    // Product Export
    Route::get('/product-bulk-export', 'ProductBulkUploadController@export')->name('product_bulk_export.index');

    // Digital Products
    Route::resource('digitalproducts', 'DigitalProductController');
    Route::get('/digitalproducts/edit/{id}', 'DigitalProductController@edit')->name('digitalproducts.edit');
    Route::get('/digitalproducts/destroy/{id}', 'DigitalProductController@destroy')->name('digitalproducts.destroy');
    Route::get('/digitalproducts/download/{id}', 'DigitalProductController@download')->name('digitalproducts.download');

    // Reports
    Route::get('/commission-log', 'ReportController@commission_history')->name('commission-log.index');

    // Coupon Form helpers
    Route::post('/coupon/get_form', 'CouponController@get_coupon_form')->name('coupon.get_coupon_form');
    Route::post('/coupon/get_form_edit', 'CouponController@get_coupon_form_edit')->name('coupon.get_coupon_form_edit');
});

