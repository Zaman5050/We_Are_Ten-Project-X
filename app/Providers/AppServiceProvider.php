<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Currency;
use App\Models\LeaveNotification;
use App\Models\User;
use App\Models\Task;
use App\Observers\TaskObserver;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use App\Helpers\Breadcrumbs;
use App\Models\Categories;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('breadcrumbs', function ($app) {
            return new Breadcrumbs();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') != 'local') {
            URL::forceScheme('https');
        }
        JsonResource::withoutWrapping();
        // Register observers
        Task::observe(TaskObserver::class);

        // Blade::component('task.modal', 'taskModal');

        // Register view composers for currencies
        View::composer(
            [
                'components.project.modal',
                'components.task.modal',
                'pages.projects.details',
                'pages.procurements.index',
                'pages.supplier.index',
                'components.procurement.*',
                'pages.product-library.index',
                'pages.settings.exchange-rate.index',
            ], // Specify the views you want to share data with
            function ($view) {
                $activeCurrencies = Currency::whereIsActive(1)
                    ->with('baseCurrencyExchangeRates.quoteCurrency:id,code')
                    ->orderBy('name', 'asc')
                    ->get();

                $view->with([
                    'activeCurrencies' => $activeCurrencies,
                ]);
            }
        );

        View::composer(
            [
                'components.project.modal',
                'pages.projects.details',
            ], // Specify the views you want to share data with
            function ($view) {
                $view->with(
                    'designers',
                    User::role(['designer'])->where('company_id', auth()->user()->company_id)->get()
                );
            }
        );
        View::composer(
            '*',
            function ($view) {
                if (auth()->check()) {
                    $user = auth()->user();
                    $notifications = LeaveNotification::where('notifiable_id', $user->id)
                        ->where('notifiable_type', get_class($user))->latest()->get();
                    $activetasks = Task::where('timer_mode', 'active')
                    ->whereHas('users', function ($query) use ($user) {
                        $query->where('user_id', auth()->user()->id);
                    }) // Ensure the task belongs to the logged-in user
                    ->with('users')->withSum('timesheet', 'total_time')
                    ->first();
                    // dd($activetasks);
                    $view->with([
                        'notifications' => $notifications,
                        'activetasks'     => $activetasks
                    ]);
                    // $view->with('notifications', $notifications);
                }
            }
        );
        View::composer(
            [
                'pages.procurements.index',
                'pages.product-library.index',
                'pages.financials.budget',
            ],
            function ($view) {
                if (auth()->check()) {
                    $categories = Categories::where('company_id', auth()->user()->company_id)->where('type', Categories::TYPE_PRODUCT)->orderBy('name')->get();
                    $view->with('categories', $categories);
                }
            }
        );
        View::composer(
            [
                'pages.scehdules.index',
                'pages.material-library.index',
                'pages.schedules.index',
            ],
            function ($view) {
                if (auth()->check()) {
                    $categories = Categories::where('company_id', auth()->user()->company_id)->where('type', Categories::TYPE_MATERIAL)->orderBy('name')->get();
                    $view->with('categories', $categories);
                }
            }
        );
    }
}
