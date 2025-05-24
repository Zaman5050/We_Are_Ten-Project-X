<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\ServiceInterfaces\UserServiceInterface;
use App\Interfaces\ServiceInterfaces\CompanyServiceInterface;
use App\Interfaces\ServiceInterfaces\HomeServiceInterface;
use App\Interfaces\ServiceInterfaces\ChatServiceInterface;

use App\Services\UserService;
use App\Services\CompanyService;
use App\Services\HomeService;
use App\Services\TaskService;
use App\Services\Tenant\ProjectService;
use App\Services\TimesheetService;
use App\Services\ChatService;

/*
        Tenant Classes
    */
use App\Interfaces\ServiceInterfaces\Tenant\HomeServiceInterface as TenantHomeServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\TaskServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\UserLeaveServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\ProjectServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\ProjectAreaServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\TimesheetServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\ScheduleServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\SupplierServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\CategoryServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\DropboxServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\ProcurementServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\FileHandlingServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\ExchangeRateServiceInterface;

use App\Services\Tenant\HomeService as TenantHomeService;
use App\Services\Tenant\UserLeaveService;
use App\Services\Tenant\ProjectAreaService;
use App\Services\Tenant\ScheduleService;
use App\Services\Tenant\SupplierService;
use App\Services\Tenant\DropboxService;
use App\Services\Tenant\ProcurementService;
use App\Services\Tenant\FileHandlingService;
use App\Services\Tenant\CategoryService;
use App\Services\Tenant\ExchangeRateService;

class ServiceInterfaceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(CompanyServiceInterface::class, CompanyService::class);
        $this->app->bind(HomeServiceInterface::class, HomeService::class);
        $this->app->bind(TenantHomeServiceInterface::class, TenantHomeService::class);
        $this->app->bind(TaskServiceInterface::class, TaskService::class);
        $this->app->bind(ProjectServiceInterface::class, ProjectService::class);
        $this->app->bind(UserLeaveServiceInterface::class, UserLeaveService::class);
        $this->app->bind(ProjectAreaServiceInterface::class, ProjectAreaService::class);
        $this->app->bind(TimesheetServiceInterface::class, TimesheetService::class);
        $this->app->bind(ScheduleServiceInterface::class, ScheduleService::class);
        $this->app->bind(SupplierServiceInterface::class, SupplierService::class);
        $this->app->bind(ChatServiceInterface::class, ChatService::class);
        $this->app->bind(DropboxServiceInterface::class, DropboxService::class);
        $this->app->bind(ProcurementServiceInterface::class, ProcurementService::class);
        $this->app->bind(FileHandlingServiceInterface::class, FileHandlingService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(ExchangeRateServiceInterface::class, ExchangeRateService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
