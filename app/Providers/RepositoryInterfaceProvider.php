<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\RepositoryInterfaces\CompanyRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\TaskRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\UserRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\ChatRepositoryInterface;

use App\Repositories\UserRepository;
use App\Repositories\TaskRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\TimesheetRepository;
use App\Repositories\ChatRepository;

/*
        Tenant Classes
    */
use App\Interfaces\RepositoryInterfaces\Tenant\ProjectRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\UserLeaveRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\ProjectAreaRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\TimesheetRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\ScheduleRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\SupplierRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\ProcurementRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\CategoryRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\ExchangeRateRepositoryInterface;

use App\Repositories\Tenant\ProjectRepository;
use App\Repositories\Tenant\UserLeaveRepository;
use App\Repositories\Tenant\ProjectAreaRepository;
use App\Repositories\Tenant\ScheduleRepository;
use App\Repositories\Tenant\SupplierRepository;
use App\Repositories\Tenant\ProcurementRepository;
use App\Repositories\Tenant\CategoryRepository;
use App\Repositories\Tenant\ExchangeRateRepository;

class RepositoryInterfaceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(UserLeaveRepositoryInterface::class, UserLeaveRepository::class);
        $this->app->bind(ProjectAreaRepositoryInterface::class, ProjectAreaRepository::class);
        $this->app->bind(TimesheetRepositoryInterface::class, TimesheetRepository::class);
        $this->app->bind(ScheduleRepositoryInterface::class, ScheduleRepository::class);
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(ChatRepositoryInterface::class, ChatRepository::class);
        $this->app->bind(ProcurementRepositoryInterface::class, ProcurementRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(ExchangeRateRepositoryInterface::class, ExchangeRateRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
