<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\User\TaskManager\ManageTasksTaskManagerUser;

Route::group(['middleware' => ['web'], 'prefix' => 'demo', 'as' => 'demo.'], function () {
    Route::get('/tasks', ManageTasksTaskManagerUser::class)->name('tasks');
});