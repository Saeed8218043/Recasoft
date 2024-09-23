<?php

// use Artisan;

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

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('download/{device}', 'DownloadFile@download')->name('ImageDownload')->middleware('auth');
Route::get('deviation-download/{id}', 'DownloadFile@deviationFileDownload')->name('deviationFileDownload')->middleware('auth');
Route::get('/pdf_download/{id}', 'PdfController@pdfDownload')->name('downloadPdf');

Route::get('/forgot-password', 'Auth\LoginController@forgotPassword')->name('forgot-password');
Route::post('/send-forgot-password', 'Auth\LoginController@forgotPasswordSend')->name('send-forgot-password');
Route::get('/reset-password/{id?}', 'Auth\LoginController@resetPassword')->name('reset-password');
Route::post('/reset-password-update', 'Auth\LoginController@resetPasswordUpdate')->name('reset-password-update');
//Dashboard2
Route::get('/dashboard2/{company_id?}', 'DemoController@index2')->name('demo');

Route::get('/events2/{device_id?}/{event_id?}', 'DemoController@getEvents')->name('events2');
Route::post('/update-order2', 'DemoController@updateOrder')->name('update-order2');


Auth::routes();
// Auth::routes(['register' => false]);
Route::get('test_send_to_server', 'CompaniesController@test_send_to_server')->name('companies.test_send_to_server');
Route::get('get_history_data/{company_id?}', 'CompaniesController@get_history_data')->name('companies.historyData');
Route::group(['middleware' => 'auth', 'guard' => 'web'], function () {
    Route::get('loadCompaniesList', 'CompaniesController@companiesList')->name('companies.companiesList');
    Route::group(['middleware' => 'roles'], function () {
        Route::get('/dashboard/{company_id?}', 'HomeController@index')->name('home');
        Route::get('/iframeDashboard/{company_id?}', 'HomeController@CloneDashboard')->name('iframeHome');
        Route::get('notifications/{company_id?}', 'NotificationController@index')->name('notifications');
        Route::get('deviations/{company_id?}', 'DeviationController@index')->name('deviations');
     
        Route::get('notifications-alertHistory/{company_id?}', 'NotificationController@alertHistory')->name('notifications.alertHistory');
        Route::get('system-log/{company_id?}', 'SystemLogController@index')->name('system.log')->middleware('superadmin');
        Route::get('create-notification/{company_id?}', 'NotificationController@createNotification')->name('create.notification');
        Route::get('/export/{company_id?}', 'SensorsController@Export')->name('export');
        Route::get('/company-settings/{company_id?}', 'CompanySettingsController@index')->name('company-settings');

        //Company Details
        Route::get('/company-details/{company_id?}', 'CompanySettingsController@companyDetails')->name('company-details');
        Route::get('/sensors/{company_id?}', 'SensorsController@index')->name('sensors');
        Route::get('/equipments/{company_id?}', 'SensorsController@equipments')->name('equipments');

        Route::get('/company-admins/{company_id?}', 'CompanySettingsController@companyAdmins')->name('company-admins');
        Route::get('notification-detail/{company_id}/{id}', 'NotificationController@Detail')->name('notification.detail');
        Route::post('notification/suggestions', 'NotificationController@suggestions')->name('notification.suggestions');
        Route::get('/sensor-details/{company_id?}/{device_id?}', 'SensorsController@Details')->name('sensor-details');
        Route::get('/equipment-details/{company_id?}/{device_id?}', 'SensorsController@equipmentDetails')->name('equipment-details');
        Route::get('/documents/{company_id?}', 'DocumentsController@index')->name('documents');
        
        
    });
    Route::post('/create-deviation', 'DeviationController@store')->name('deviations.create');
    
    Route::get('/sub-documents-In-Resource', 'DocumentInResourceController@subFolders')->name('sub-documents.in.resource');
    Route::get('/documents-In-Resource', 'DocumentInResourceController@index')->name('documents.in.resource');
    Route::get('/connect-with-sensor', 'SensorsController@connectWithSensor')->name('connect.with.sensor');
    Route::get('/connect-with-equipment', 'SensorsController@connectWithEquipment')->name('connect.with.equipment');
    Route::post('/sensor-connect', 'SensorsController@sensorConnection')->name('sensor.connection');
    Route::post('/sensor-disconnect', 'SensorsController@sensorDisconnect')->name('sensor.disconnect');
    Route::post('/documents-copyFolder', 'DocumentsController@checkchildren')->name('documents.copyFolder');
    Route::get('/documents-syncFolder', 'DocumentsController@syncFolder')->name('documents.syncFolder');
    Route::post('/delete-deviation/{id}', 'DeviationController@destroy')->name('deviations.delete');
    Route::post('/edit-deviation', 'DeviationController@update')->name('deviations.edit');

    Route::resource('companies', 'CompaniesController')->names('companies');

    Route::post('companies/store2/{par_id}', 'CompaniesController@store2')->name('companies.store2');
    Route::post('companies/storeCompanyWithApi/', 'CompaniesController@storeCompanyWithApi')->name('companies.storeCompanyWithApi');
    Route::get('search-project', 'CompaniesController@SearchProject')->name('search-project');




    //////////////// Notifications routes


    Route::post('store/notification', 'NotificationController@storeNotification')->name('store.notification');
    Route::post('get/devices', 'NotificationController@getDevices');
    Route::post('search/devices', 'NotificationController@searchDevices');
    Route::post('get/single/device', 'NotificationController@getSingleDevice');

    Route::post('add/email', 'NotificationController@addEmail');
    Route::post('add/sms', 'NotificationController@addSMS');
    Route::get('delete/notification/{id}', 'NotificationController@deleteNotification');

    Route::post('update/notification', 'NotificationController@updateNotification');



    Route::get('showDataCompanies', 'CompaniesController@showData')->name('companies.showData');

    Route::get('loadDeviceURL', 'CompaniesController@loadDeviceURL')->name('companies.loadDeviceURL');
    Route::post('moveSensor', 'CompaniesController@moveSensor')->name('companies.moveSensor');

    Route::post('moveSensor2', 'CompaniesController@moveSensor2')->name('companies.moveSensor2');
    Route::post('uploadSensorDoc', 'CompaniesController@uploadSensorDoc')->name('companies.uploadSensorDoc');
    Route::post('uploadSensorDoc2', 'CompaniesController@uploadSensorDoc2')->name('companies.uploadSensorDoc2');

    Route::post('deleteNote/{id?}', 'CompaniesController@deleteNote')->name('companies.deleteNote');
    Route::post('equipmentDelete/{id?}', 'SensorsController@deleteEquipment')->name('equipment.delete');
    Route::post('inventoryDelete/{id?}', 'SensorsController@inventoryDelete')->name('inventory.delete');
    Route::post('editNote/{id?}', 'CompaniesController@editNote')->name('companies.editNote');
    Route::get('ViewNote/{company_name}/{id?}', 'PdfController@Note_pdfDownload')->name('companies.ViewNote');
    Route::get('viewNoteValue/{id?}', 'CompaniesController@viewNoteValue')->name('companies.viewNoteValue');
    Route::get('viewFolderValue/{id?}', 'DocumentsController@viewFolderValue')->name('documents.viewFolderValue');
    Route::post('uploadSensorNotes', 'CompaniesController@uploadSensorNotes')->name('companies.uploadSensorNotes');
    Route::post('deleteDoc/{id?}', 'CompaniesController@deleteDoc')->name('companies.deleteDoc');

    Route::post('moveSensors/abc', 'CompaniesController@moveSensors')->name('companies.moveSensors');



    Route::post('/copyEquipment', 'SensorsController@copyEquipments')->name('copyEquipment');
    Route::get('/documents-downloadFile/{id}', 'DocumentsController@downloadFile')->name('documents.downloadFile');
    Route::post('/documents-create/{company_id?}', 'DocumentsController@store')->name('documents.create');
    Route::post('/documents-createsub/{company_id}/{slug}', 'DocumentsController@createSubFolder')->name('documents.createsub');
    Route::post('/documents-createFile/{company_id}/{slug}', 'DocumentsController@createFile')->name('documents.createFile');
    Route::get('/documents-deletefolder/{id}', 'DocumentsController@destroy')->name('documents.deletefolder');
    Route::post('/documents-updateFolder/{id?}', 'DocumentsController@update')->name('documents.editFolder');
    Route::get('/documents/{company_id}/{slug}', 'DocumentsController@subfolders')->name('documents.subfolders');
    Route::get('/check/sensor', 'SensorsController@checkSensor')->name('check.sensor');
    Route::get('/sendOrder-service/{company_id?}', 'SendOrderServiceController@index')->name('sendOrder.service');
    Route::get('/order-service-logs/{company_id?}', 'SendOrderServiceController@logs')->name('sendOrder.logs');



    Route::post('/equipmentStore', 'SensorsController@equipmentStore')->name('equipmentStore');
    Route::post('/inventoryStore', 'SensorsController@inventoryStore')->name('inventoryStore');
    Route::get('/history-details/{device_id?}/{val?}', 'SensorsController@getHistoryData')->name('history-details');
    Route::get('/history-details-ccon/{device_id?}/{val?}', 'SensorsController@getHistoryDataConnector')->name('history-details-ccon');
    Route::get('/history-details-ccon2/{device_id?}/{val?}', 'SensorsController@getHistoryDataConnector_sensor')->name('history-details-ccon2');
    Route::post('/updateSensorDetails/{device_id?}', 'SensorsController@updateSensorDetails')->name('updateSensorDetails');

    Route::get('/export-csv/{company_id?}/{startdate?}/{enddate?}/{device_id?}/{file_name?}', 'SensorsController@ExportCSV')->name('export-csv');
    Route::match(['post', 'get'],'/export-csv-by-date', 'SensorsController@ExportCSVByDate')->name('export-csv-by-date');


    Route::delete('/company-settings/{company_id?}', 'CompanySettingsController@deleteCompany')->name('company-settings.deleteCompany');

    Route::post('/add-notification-email', 'NotificationController@AddNotificationEmail')->name('add-notification-email');
    Route::post('/editRecipient', 'NotificationController@editRecipient')->name('editRecipient');
    Route::post('/add-notification-number', 'NotificationController@AddNotificationNumber')->name('add-notification-number');
    Route::post('/update-company-settings', 'CompanySettingsController@UpdateCompanySettings')->name('update-company-settings');
    Route::post('/update-company-image', 'CompanySettingsController@UpdateCompanyImage')->name('update-company-image');
    Route::post('/delete-company-image', 'CompanySettingsController@DeleteCompanyImage')->name('delete-company-image');
    Route::post('/invite-member', 'CompanySettingsController@inviteMember')->name('invite-member');
    Route::post('/invite-admin', 'CompanySettingsController@inviteAdmin')->name('invite-admin');

    Route::get('test', 'CompaniesController@Test')->name('companies.test');
    Route::get('invitation', 'CompanySettingsController@invitation')->name('invitation');
    Route::get('delete-from-company/{email?}/{company_id?}', 'CompanySettingsController@deleteFromCompany')->name('delete-from-company');
    Route::get('delete-email/{email?}/{company_id?}', 'CompanySettingsController@deleteEmail')->name('delete-email');
    Route::get('delete-number/{email?}/{company_id?}', 'CompanySettingsController@deleteNumber')->name('delete-number');

    Route::get('/events/{device_id?}/{event_id?}', 'HomeController@getEvents')->name('events');
    Route::post('/update-order', 'HomeController@updateOrder')->name('update-order');

 

    //Company Admins

    Route::post('/updateSettings/{company_id?}', 'CompanySettingsController@updateSettings')->name('updateSettings');
    Route::post('/sendOrderService/{company_id?}', 'CompanySettingsController@sendOrderService')->name('sendOrderService');
    Route::get('/mailCheck', function () {
        return view('emails.sendOrderService', ['company_name' => 'Europe', 'deviceNames' => ['abc', 'sdfs', 'sdfdsf'], 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.']);
    });

    /*Route::get('/pusher', function() {
    event(new App\Events\HelloPusherEvent('123'));
    return "Event has been sent!";
});*/
});


Route::get('/account/register/{id?}', 'CompanySettingsController@createAccount');
Route::post('create-user', 'CompanySettingsController@createUser')->name('create-user');
Route::get('login-user/{id?}/{email?}', 'CompanySettingsController@loginUser')->name('login-user');
Route::get('abc', 'Auth\LoginController@abc')->name('abc');
Route::get('abc123', 'CompanySettingsController@abc123')->name('abc123');

Route::get('/clear-config', function(){
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    return 'cache cleared';
});
Route::any('/sendNotification','CronController@sendNotification');
Route::any('/sendresolved','DataController@sendResolvedNotification');
Route::any('/sendemail','CronController@sendNotification');
Route::any('/postData/test','DataController@postData');
Route::post('/claim/sensor', 'DataController@claimSensor')->name('claim.sensor');
Route::get('/testfunction','DataController@testFunction');
Route::post('claimSensor','DataController@claimSensorPost');
Route::get('reminder/test','DataController@testReminder');
