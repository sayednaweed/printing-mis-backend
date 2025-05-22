<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\User\UserRepository;
use App\Repositories\Storage\StorageRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Permission\PermissionRepository;
use App\Repositories\PendingTask\PendingTaskRepository;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\Notification\NotificationRepository;
use App\Repositories\Permission\PermissionRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;
use App\Repositories\Notification\NotificationRepositoryInterface;
use App\Repositories\Party\PartyRepository;
use App\Repositories\Party\PartyRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(PendingTaskRepositoryInterface::class, PendingTaskRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(StorageRepositoryInterface::class, StorageRepository::class);
        $this->app->bind(PartyRepositoryInterface::class, PartyRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
