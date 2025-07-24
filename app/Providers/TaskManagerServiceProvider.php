<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class TaskManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register existing Services as singletons
        $this->app->singleton(\App\Services\TaskService::class);
        $this->app->singleton(\App\Services\TagService::class);

        // Register Use Cases as singletons for better performance
        $this->app->singleton(\App\UseCases\CreateTaskUseCase::class);
        $this->app->singleton(\App\UseCases\UpdateTaskUseCase::class);
        $this->app->singleton(\App\UseCases\CompleteTaskUseCase::class);
        $this->app->singleton(\App\UseCases\DeleteTaskUseCase::class);
        $this->app->singleton(\App\UseCases\CreateTagUseCase::class);
        $this->app->singleton(\App\UseCases\GetActiveTasksUseCase::class);
        $this->app->singleton(\App\UseCases\GetAllTagsUseCase::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutes();
        $this->loadViews();
        $this->loadMigrations();
    }

    /**
     * Load module routes
     */
    protected function loadRoutes(): void
    {
        // Load role-based routes
        $routePath = base_path('routes');
        
        if (file_exists($routePath . '/user.php')) {
            Route::middleware('web')
                ->group($routePath . '/user.php');
        }
        
        if (file_exists($routePath . '/admin.php')) {
            Route::middleware('web')
                ->group($routePath . '/admin.php');
        }
        
        if (file_exists($routePath . '/guest.php')) {
            Route::middleware('web')
                ->group($routePath . '/guest.php');
        }
    }

    /**
     * Load module views
     */
    protected function loadViews(): void
    {
        $this->loadViewsFrom(
            resource_path('views'),
            'taskmanager'
        );
    }

    /**
     * Load module migrations
     */
    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            database_path('migrations')
        );
    }
}
