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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/get-categories','BigCommerceController@getCategories');
Route::get('/create-category','BigCommerceController@createCategory');
Route::get('/get-single-category','BigCommerceController@getSingleCategory');
Route::get('/delete-category','BigCommerceController@deleteCategory');
Route::post('/put-category','BigCommerceController@getCategories');

Route::get('/get-customers','BigCommerceController@getCustomers');
Route::get('/create-customer','BigCommerceController@createCustomer');
Route::get('/get-single-customer','BigCommerceController@getSingleCustomer');
Route::get('/delete-customer','BigCommerceController@deleteCustomer');
Route::get('/delete-all-customers','BigCommerceController@deleteCustomers');
Route::post('/put-customer','BigCommerceController@postCustomers');

Route::get('/get-customer-addresses','BigCommerceController@getCustomerAddresses');
Route::get('/create-customer-address','BigCommerceController@createCustomerAddress');
Route::get('/get-single-customer-address','BigCommerceController@getSingleCustomerAddress');
Route::get('/delete-customer-address','BigCommerceController@deleteCustomerAddress');
Route::post('/put-customer-address','BigCommerceController@postCustomerAddress');
Route::get('/get-customer-address-check','BigCommerceController@getCustomerAddressCheck');


Route::get('/get-products','BigCommerceController@getProducts');
Route::get('/create-product','BigCommerceController@createProduct');
Route::get('/update-product','BigCommerceController@updateProduct');
Route::get('/get-single-product','BigCommerceController@getSingleProduct');
Route::get('/delete-product','BigCommerceController@deleteProduct');
Route::post('/put-product','BigCommerceController@postProduct');

Route::get('/create-new-options','BigCommerceController@createNewOptions'); //OK
Route::get('/create-option','BigCommerceController@createOption');
Route::get('/get-single-option','BigCommerceController@getOption');
Route::get('/delete-option','BigCommerceController@deleteOption');
Route::post('/put-option','BigCommerceController@postOption');

Route::get('/get-brands','BigCommerceController@getBrands');
Route::get('/create-brand','BigCommerceController@createBrand');
Route::get('/get-single-brand','BigCommerceController@Brand');
Route::get('/delete-brand','BigCommerceController@deleteBrand');
Route::get('/delete-all-brands','BigCommerceController@deleteAllBrands');
Route::post('/put-brand','BigCommerceController@postBrand');

Route::get('/get-options','BigCommerceController@getOptions');
Route::get('/create-options','BigCommerceController@createOption');
Route::get('/update-options','BigCommerceController@updateOption');
Route::get('/get-options-count','BigCommerceController@getOptionsCount');
Route::get('/get-option','BigCommerceController@getOption');
Route::get('/delete-option','BigCommerceController@deleteOption');
Route::get('/get-option-value','BigCommerceController@getOptionValue');
Route::get('/get-option-values','BigCommerceController@getOptionValues');
Route::get('/get-option-sets','BigCommerceController@getOptionSets');
Route::get('/get-option-set','BigCommerceController@getOptionSet');
Route::get('/create-option-set','BigCommerceController@createOptionSet');
Route::get('/create-option-set-option','BigCommerceController@createOptionSetOption');
Route::get('/get-option-set-option','BigCommerceController@getOptionSetOption');
Route::get('/get-option-set-options','BigCommerceController@getOptionSetOptions');
Route::get('/get-option-sets-count','BigCommerceController@getOptionSetsCount');
Route::get('/update-option-set','BigCommerceController@updateOptionSet');
Route::get('/delete-option-set','BigCommerceController@deleteOptionSet');
Route::get('/delete-all-options','BigCommerceController@deleteAllOptions');
Route::get('/get-product-options','BigCommerceController@getProductOptions');
Route::get('/get-product-option','BigCommerceController@getProductOption');
Route::get('/create-option-value','BigCommerceController@createOptionValue');
Route::get('/delete-all-option-sets','BigCommerceController@deleteAllOptionSets');
Route::get('/update-option-value','BigCommerceController@updateOptionValue');

Route::get('/option-set-process','BigCommerceController@optionSetProcess');
Route::get('/option-process','BigCommerceController@optionProcess');
Route::get('/create-product-custom-field','BigCommerceController@createProductCustomField');
Route::get('/get-product-custom-fields','BigCommerceController@getProductCustomFields');
Route::get('/delete-product-custom-fields','BigCommerceController@deleteProductCustomField');

Route::get('/create-product-custom-field-product-type','BigCommerceController@createProductCustomFieldProductType');
Route::get('/create-product-custom-field-uom','BigCommerceController@createProductCustomFieldUom');

Route::get('/create-new-option-values','BigCommerceController@createNewOptionValues'); //OK

Route::get('/job-contract-products-to-categories','BigCommerceController@jobContractProductsToCategories'); //OK

Route::get('/customers-force-reset-passwords','BigCommerceController@getCustomersResetTheirPasswords'); //OK

Route::get('/create-job-contracter-users','BigCommerceController@createJobContracterUsers'); //OK


Route::get('/update-option-set-options','BigCommerceController@updateOptionSetOptions'); //OK

Route::get('/get-customer-groups','BigCommerceController@getCustomerGroups');

Route::get('/get-single-customer-group','BigCommerceController@getCustomerGroup');