<?php

Route::post('save_history', 'HistoryController@save_history');
Route::post('check_user', 'ThirdPartyAuthController@check_user');
Route::get('download-consent-form', 'PDFController@download');
Route::post('export-exam-result', 'ExamResultController@export_exam_result')->name('reports.export_exam_result');
