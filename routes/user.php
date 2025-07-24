<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\User\TaskManager\ManageTasksTaskManagerUser;

Route::group(['middleware' => ['web'], 'prefix' => 'tasks', 'as' => 'tasks.'], function () {
    Route::get('/', ManageTasksTaskManagerUser::class)->name('manage');
    Route::get('/manage', ManageTasksTaskManagerUser::class)->name('index');
});