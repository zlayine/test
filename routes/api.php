<?php

use Illuminate\Http\Request;


Route::group(['prefix' => 'auth'], function($router){
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
});

Route::group(['middleware' => 'jwt.auth'], function ($router) {
    Route::get('/campaign/stats', 'CampaignController@getStats');
    Route::get('/campaign/test/{id}', 'CampaignController@test');
    Route::get('/campaign/all', 'CampaignController@getCampaigns');
    Route::get('/campaign/{id}', 'CampaignController@getCampaign');
    Route::post('/campaign/save', 'CampaignController@saveCampaign');
    Route::get('/campaign/duplicate/{id}', 'CampaignController@duplicateCampaign');
    Route::get('/campaign/launch/{id}', 'CampaignController@launchCampaign');
    
    Route::get('/campaign/retry/{id}', 'CampaignController@retryCampaign');
    Route::get('/campaign/stats/{id}', 'CampaignController@getStatsById');

    Route::post('/campaign/file/attach', 'FileController@AttachFileAttachement');
    Route::post('/campaign/file/detach', 'FileController@detachFile');
    Route::get('/campaign/delete/{id}', 'CampaignController@deleteCampaign');

    Route::get('/smtp/group/all', 'SmtpController@getGroups');
    Route::get('/smtp/{id}', 'SmtpController@getSmtps');
    Route::post('/smtp/add', 'SmtpController@addSmtp');
    Route::post('/smtp/group/add', 'SmtpController@addGroup');
    Route::post('/smtp/group/name', 'SmtpController@updateGroup');
    Route::get('/smtp/delete/{id}', 'SmtpController@deleteSmtp');
    Route::get('/smtp/group/delete/{id}', 'SmtpController@deleteGroup');

    Route::get('/files', 'FileController@getFiles');
    Route::post('/files/add', 'FileController@addFile');
    Route::post('/file/name', 'FileController@updateFile');
    Route::post('/files/merge', 'FileController@mergeFiles');
    Route::get('/file/delete/{id}', 'FileController@deleteFile');

    Route::post('/settings/email', 'SettingsController@updateEmail');
    Route::get('/campaign/launch/debug/{id}', 'CampaignController@launchDebug');
    Route::get('/settings', 'SettingsController@getSettings');
    
});
Route::post('/create', 'SettingsController@createUser');
Route::get('/check', 'SettingsController@checkUser');
Route::get('/test', 'SettingsController@test');
