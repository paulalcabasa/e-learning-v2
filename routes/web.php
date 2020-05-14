<?php

Auth::routes();

// ================= session authentication ========================= //
Route::get('admin/login/{employee_id}/{employee_no}/{full_name}/{section}', 'Auth\AuthSessionController@login');
// ================= end ================ //

Route::get('/', 'RedirectLoginController@login');
Route::post('/check_logging', 'ThirdPartyAuthController@check_logging');

Route::middleware(['auth'])->group(function () {        //--> Master middleware for CLIENT'S AUTH:

	Route::middleware(['trainor'])->group(function () { //--> For TRAINOR'S MIDDLEWARE

		// Contact Us
		Route::post('trainor/send_notification', 'NotificationController@send_notification');

		// Reset Password
		Route::put('/trainor/reset_password/{id}/{user_type}', 'UserProfileController@reset_password');

		Route::put('/trainor/done_reading_pdf/{module_detail_id}', 'TrainorModuleController@done_reading_pdf');

		Route::get('/trainor/calendar', 'CalendarController@get_events')->name('calendar');

		Route::get('/trainor/profile/get/{trainor_id}', 'UserProfileController@trainor_profile');
		Route::put('/trainor/profile/update/{trainor_id}', 'UserProfileController@update_profile');

		Route::get('/trainor/trainee_results/get/{exam_schedule_id}/{trainor_id}', 'TrainorSchedulesController@trainees');
		Route::get('/trainor/trainee_schedules/get/{trainor_id}', 'TrainorSchedulesController@schedules');

		Route::put('/trainor/trainee/put/{trainee_id}', 'TraineeController@update');
		Route::get('/trainor/trainee/get/{trainee_id}', 'TraineeController@show');
		Route::delete('/trainor/trainee/delete/{trainee_id}', 'TraineeController@destroy');
		Route::post('/trainor/trainee/post', 'TraineeController@store');
		Route::put('/trainor/trigger_module/{module_detail_id}/{user_id}', 'ModuleDetailController@trigger_module');
		Route::get('/trainor/trainor_modules/get/{trainor_id}', 'TrainorModuleController@trainor_modules');
		Route::get('/trainor/trainee_list/get/{trainor_id}', 'TraineeListController@trainee_list');

		/** Views */
		Route::view('/trainor/contact_us', 'trainor.contact_us');
		Route::view('/trainor/profile', 'trainor.profile');
		Route::view('/trainor/trainee_results/exam_schedule_id/{exam_schedule_id}', 'trainor.trainee_results');
		Route::view('/trainor/trainee_schedules', 'trainor.trainee_schedules')->name('trainee_schedules');
		Route::view('/trainor/trainee_list', 'trainor.trainee_list')->name('trainee_list');
		
		
		Route::get('/trainor/category', 'CategoryController@trainorCategories')->name('trainor');
		Route::get('/trainor/modules/{category_id}', 'TrainorController@modules');
		Route::get('/category/get', 'CategoryController@index');

		/** PDF */
		// Route::view('download-consent-form', 'PDFController@download');
	});

	Route::middleware(['trainee'])->group(function () { //--> For TRAINEE'S MIDDLEWARE

		Route::get('/trainee/profile/get/{trainee_id}', 'UserProfileController@trainee_profile');
		Route::put('/trainee/profile/update/{trainee_id}', 'UserProfileController@trainee_update_profile');
		
		Route::post('/trainee/has_blank_answer', 'TraineeExamController@has_blank_answer');
		Route::post('/trainee/remaining_time/update', 'TraineeExamController@save_time');

		Route::put('/trainee/timers_up/{trainee_question_id}', 'TraineeExamController@timers_up');
		Route::post('/trainee/update_answered_choice', 'TraineeExamController@update_choice_answered');
		Route::put('/trainee/answer/{trainee_question_id}', 'TraineeExamController@answer');
		Route::post('/trainee/question', 'TraineeExamController@question');
		Route::get('/trainee/exam_content/get/{exam_detail_id}', 'TraineeExamController@exam_content');
		Route::put('/trainee/trigger_exam/{exam_detail_id}', 'TraineeExamController@trigger_exam');
		Route::get('/trainee/list_of_exams/get/{trainee_id}', 'TraineeExamController@list_of_exams');
		
		/** Views */
		Route::view('/trainee/profile', 'trainee.profile');
		Route::view('/trainee/exam/{exam_detail_id}', 'trainee.exam_content')->name('exam');
		Route::view('/trainee', 'trainee.trainee')->name('trainee');
	});

	/** Logout */
	Route::post('/user/logout', 'ThirdPartyAuthController@logout');

	/** Authenticated Views */
	Route::view('/pdf_viewer/{file_name}/{module_detail_id}/{category_id}', 'pdf_viewer');
});

// Routes for middlewares here .. soon to modify
Route::middleware(['check_session'])->group(function () { //--> For Administrator

	// Route to IPC Home
	Route::get('ipc_home', 'IPCHomeController@ipc_home')->name('ipc_home');

	// Update Trainee status ('approve' & 'disapprove)
	Route::put('admin/update_trainee_status/{trainee_id}/{status}', 'UpdateTraineeStatusController@update_trainee');

	// History
	Route::get('admin/trainee_history/{trainee_id}', 'HistoryController@get_trainee_history');
	Route::get('admin/trainor_history/{trainor_id}', 'HistoryController@get_trainor_history');
	Route::get('admin/trainee/{trainee_id}', 'HistoryController@get_trainee');
	Route::get('admin/trainor_history/{trainor_id}', 'TrainorHistoryController@get_history');

	// Gateway to ecommerce5
	Route::get('/admin/user_access/get/{employee_id}', 'UserAccessController@index');

	// Calendar
	Route::get('/admin/calendar/get', 'AdminCalendarController@get_events');

	// Reset Password
	Route::put('/admin/reset_password/{id}/{user_type}', 'UserProfileController@reset_password');

	// Archives
	Route::get('/admin/archives/archive_trainors/get', 'ArchiveController@archive_trainors');
	Route::put('/admin/archives/retrieve_trainors/update/{trainor_id}', 'ArchiveController@retrieve_trainor');
	Route::delete('/admin/archives/delete_trainor/delete/{trainor_id}', 'ArchiveController@delete_trainor');
	
	// Modules
	Route::get('/admin/modules/get', 'ModuleController@modules');
	Route::get('/admin/modules', 'ModuleController@index');
	Route::get('/admin/get_modules', 'ModuleController@get');
	Route::post('/admin/create_module', 'ModuleController@store');
	Route::delete('/admin/delete_module/{id}', 'ModuleController@destroy');

	Route::get('/admin/modules/{id}', 'ModuleController@show');
	Route::get('/admin/module/display_pdf/{id}', 'ModuleController@display_pdf');
	Route::get('/admin/get_module/{id}', 'ModuleController@get_module');
	Route::put('/admin/update_module/{id}', 'ModuleController@update');
	Route::put('/admin/upload_pdf/{id}', 'ModuleController@upload_pdf');

	// Submodules
	Route::get('/admin/submodules/{id?}', 'SubModuleController@index');
	Route::get('/admin/submodules/get/{module_id}', 'SubModuleController@submodules');
	Route::get('/admin/submodule/get/{submodule_id}', 'SubModuleController@submodule');
	Route::post('/admin/submodules/create', 'SubModuleController@store');
	Route::put('/admin/submodules/update/{submodule_id}', 'SubModuleController@update');
	Route::delete('/admin/submodules/delete/{id}', 'SubModuleController@destroy');

	// Submodule with Questions and Choices
	Route::get('/admin/submodule/mod_sub_details/{submodule_id?}', 'SubModuleController@mod_sub_details');
	Route::post('/admin/submodule/create', 'SubModuleController@create_qa');
	Route::get('/admin/choices/get/{question_id}', 'SubModuleController@choices');

	// Questions and Choices
	Route::get('/admin/submodule/{submodule_id}/questions', 'QuestionController@index');
	Route::get('/admin/submodule/{submodule_id}/questions/create', 'QuestionController@create');
	Route::post('/admin/submodule/{submodule_id}/questions/store', 'QuestionController@store');
	Route::get('/admin/submodule/{submodule_id}/questions/edit/{question_id}', 'QuestionController@edit');
	Route::put('/admin/submodule/{submodule_id}/questions/update/{question_id}', 'QuestionController@update');
	Route::delete('/admin/submodule/{submodule_id}/questions/delete/{question_id}', 'QuestionController@destroy');

	// Dealers
	Route::get('/admin/dealers/get', 'DealerController@index');
	Route::get('/admin/dealers/get/{dealer_id}', 'DealerController@show');
	Route::post('/admin/dealers/post', 'DealerController@store');
	Route::put('/admin/dealers/put/{dealer_id}', 'DealerController@update');
	Route::delete('/admin/dealers/delete/{dealer_id}', 'DealerController@destroy');

	// Trainors
	Route::get('/admin/trainors/get', 'TrainorController@index');
	Route::get('/admin/trainors/get/{trainor_id}', 'TrainorController@show');
	Route::post('/admin/trainors/post', 'TrainorController@store');
	Route::put('/admin/trainors/put/{trainor_id}', 'TrainorController@update');
	Route::delete('/admin/trainors/delete/{trainor_id}', 'TrainorController@destroy');
	Route::get('/admin/trainor/categories/{trainor_id}', 'TrainorCategoryController@get');
	Route::post('/admin/trainor/category/save', 'TrainorCategoryController@store');

	// Trainees
	Route::get('/admin/trainees/get', 'TraineeController@index');
	Route::get('/admin/trainees/get/{trainee_id}', 'TraineeController@show');
	Route::post('/admin/trainees/post', 'TraineeController@store');
	Route::put('/admin/trainees/put/{trainee_id}', 'TraineeController@update');
	Route::delete('/admin/trainees/delete/{trainee_id}', 'TraineeController@destroy');
	Route::put('/admin/trainees/approve/{trainee_id}', 'TraineeController@approve_registration');

	// ModuleSchedules
	Route::get('/admin/module_schedules/get', 'ModuleScheduleController@index');
	Route::get('/admin/module_schedules/get/{module_schedule_id}', 'ModuleScheduleController@show');
	Route::post('/admin/module_schedules/post', 'ModuleScheduleController@store');
	Route::put('/admin/module_schedules/put/{module_schedule_id}', 'ModuleScheduleController@update');
	Route::delete('/admin/module_schedules/delete/{module_schedule_id}', 'ModuleScheduleController@destroy');

	Route::put('/admin/module_schedules/update_status/{module_id}', 'ModuleScheduleController@update_status');

	// ModuleDetails
	Route::get('/admin/module_details/get', 'ModuleDetailController@index');
	Route::get('/admin/module_details/get/{module_detail_id}', 'ModuleDetailController@show');
	Route::post('/admin/module_details/post', 'ModuleDetailController@store');
	Route::put('/admin/module_details/put/{module_detail_id}', 'ModuleDetailController@update');
	Route::delete('/admin/module_details/delete/{module_detail_id}', 'ModuleDetailController@destroy');

	Route::get('/admin/dealers_schedule/get/{module_schedule_id}', 'ModuleDetailController@dealers_schedule');
	Route::put('/admin/module_details/disabling_module/{module_detail_id}', 'ModuleDetailController@disabling_module');

	// ExamSchedules
	Route::get('/admin/exam_schedules/get', 'ExamScheduleController@index');
	Route::get('/admin/exam_schedules/get/{exam_schedule_id}', 'ExamScheduleController@show');
	Route::post('/admin/exam_schedules/post', 'ExamScheduleController@store');
	Route::put('/admin/exam_schedules/put/{exam_schedule_id}', 'ExamScheduleController@update');
	Route::delete('/admin/exam_schedules/delete/{exam_schedule_id}', 'ExamScheduleController@destroy');
	Route::put('/admin/exam_schedules/update_timer/{exam_schedule_id}', 'ExamScheduleController@update_timer');
	Route::put('/admin/exam_schedules/update_passing_score/{exam_schedule_id}', 'ExamScheduleController@update_passing_score');

	// ExamDetails
	Route::get('/admin/exam_details/get', 'ExamDetailController@index');
	Route::get('/admin/exam_details/get/{exam_detail_id}', 'ExamDetailController@show');
	Route::post('/admin/exam_details/post', 'ExamDetailController@store');
	Route::put('/admin/exam_details/put/{exam_detail_id}', 'ExamDetailController@update');
	Route::delete('/admin/exam_details/delete/{exam_detail_id}', 'ExamDetailController@destroy');
	Route::get('/admin/dealers_exam_schedule/get/{exam_schedule_id}', 'ExamDetailController@dealers_exam_schedule');

	// QuestionDetails
	Route::get('/admin/question_details/get', 'QuestionDetailController@index');
	Route::get('/admin/question_detail/get/{question_detail_id}', 'QuestionDetailController@show');
	Route::post('/admin/question_detail/post', 'QuestionDetailController@store');
	Route::put('/admin/question_detail/put/{question_detail_id}', 'QuestionDetailController@update');
	Route::delete('/admin/question_detail/delete/{question_detail_id}', 'QuestionDetailController@destroy');

	// Check Expiration of modules and exams
	Route::put('/admin/update_module_status/{module_detail_id}', 'ExpiryController@update_module_status');
	Route::put('/admin/update_exam_status/{exam_detail_id}', 'ExpiryController@update_exam_status');

	// Exam Results
	Route::get('admin/schedule_summary/{exam_schedule_id}', 'ExamResultController@schedule_summary');
	Route::get('/admin/dealer_summary/{dealer_id}/{exam_schedule_id}', 'ExamResultController@dealer_summary');
	Route::post('/admin/results/summary', 'ExamResultController@summary');
	Route::get('/admin/results/exam_status/{exam_schedule_id}/{dealer_id}/{trainee_id}', 'ExamResultController@exam_status');
	Route::get('/admin/results/header/{exam_schedule_id}', 'ExamResultController@header');
	Route::get('/admin/results/trainee/{trainee_id}', 'TraineeController@show');
	Route::get('/admin/results/correct_answer/get/{trainee_question_id}', 'ExamResultController@correct_answer');
	Route::get('/admin/results/detailed_result/{exam_schedule_id}/{trainee_id}', 'ExamResultController@detailed_result');
	Route::get('/admin/results/trainees/get/{exam_schedule_id}/{dealer_id}/', 'ExamResultController@trainees');
	Route::get('/admin/results/dealers/get/{exam_schedule_id}', 'ExamResultController@dealers');
	Route::get('/admin/results/exam_schedules/get', 'ExamResultController@exam_schedules');
	Route::get('/admin/results/dealer_average/get/{exam_schedule_id}', 'ExamResultController@dealer_average');

	// Users
	Route::get('/admin/controls/users/get', 'ActiveUserController@users');
	Route::post('/admin/controls/logout', 'ActiveUserController@logout');

	/** Admin Views */
	Route::view('/admin', 'contents.dealers.dealers')->name('admin');
	Route::view('/admin/dealers', 'contents.dealers.dealers');
	Route::view('/admin/trainors', 'contents.trainors.trainors');
	Route::view('/admin/trainees', 'contents.trainees.trainees');
	Route::view('/admin/module_schedules', 'contents.module_details.module_schedules');
	Route::view('/admin/module_schedules/create', 'contents.module_details.create_schedule');
	Route::view('/admin/module_schedule_id/{module_schedule_id}/module_details', 'contents.module_details.module_details');
	Route::view('/admin/module_schedule_id/{module_schedule_id}/module_details/create', 'contents.module_details.create_module_details');
	Route::view('/admin/exam_schedules', 'contents.exam_details.exam_schedules');
	Route::view('/admin/exam_schedules/create', 'contents.exam_details.create_schedule');
	Route::view('/admin/exam_schedule_id/{exam_schedule_id}/exam_details', 'contents.exam_details.exam_details');
	Route::view('/admin/exam_schedule_id/{exam_schedule_id}/exam_details/create', 'contents.exam_details.create_exam_details');

	Route::view('/admin/results/exam_schedules', 'contents.exam_results.exam_schedules');
	Route::view('/admin/results/exam_schedules/{exam_schedule_id}/dealers', 'contents.exam_results.dealers');
	Route::view('/admin/results/exam_schedules/{exam_schedule_id}/dealers/{dealer_id}', 'contents.exam_results.trainees');
	Route::view('/admin/results/exam_schedules/{exam_schedule_id}/dealers/{dealer_id}/trainees/{trainee_id}', 'contents.exam_results.trainee');

	Route::view('/admin/controls/active_users', 'contents.controls.active_users');

	Route::view('/admin/archives/archive_trainors', 'contents.archives.archive_trainors');
	Route::view('admin/trainee_details/{trainee_id}', 'contents.trainees.details');


	/*  Categories */
	Route::view('/admin/categories', 'contents.category.categories');
	Route::post('/admin/category/add', 'CategoryController@store');
	Route::post('/admin/category/update', 'CategoryController@update');
	Route::get('/admin/category/get', 'CategoryController@index');

	Route::get('/admin/category/admin/get/{category_id}', 'CategoryAdminController@index');
	Route::post('/admin/category/admin/save', 'CategoryAdminController@store');

	
	Route::post('/admin/classification/save', 'ClassificationController@store');
	Route::get('/admin/classification/get/{category_id}', 'ClassificationController@show');
});

Route::get('/flush_session', 'SessionSampleController@flush_session');

/** Error Page */
Route::view('/administration_guard', 'error_pages.administration_guard')->name('administration_guard');
Route::view('/user_blocked', 'error_pages.page_blocked')->name('page_blocked');

// Testingannnn ..
Route::get('/send_fake_email', 'SendEmailController@send_bulk_emails');
Route::get('test/{exam_schedule_id}', 'ExamResultController@scoring_summary');
