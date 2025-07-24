<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\User\TaskManager\ManageTasksTaskManagerUser;

Route::group(['middleware' => ['web'], 'prefix' => 'admin/tasks', 'as' => 'admin.tasks.'], function () {
    Route::get('/', ManageTasksTaskManagerUser::class)->name('manage');
    Route::get('/overview', ManageTasksTaskManagerUser::class)->name('overview');
});