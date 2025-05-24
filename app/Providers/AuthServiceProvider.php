<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Models\Project;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        Gate::define('isAdmin', function ($user) {
            return $user->hasRole('admin');
        });
        

        Gate::define('isDesigner', function ($user) {
            return $user->hasRole('designer');
        });

        Gate::define('isSuperAdmin', function ($user) {
            return $user->hasRole('super-admin');
        });

        Gate::define('isProcure', function ($user) {
            $projectId = request()->project ? request()->project : (request()->projectUuid ? request()->projectUuid : '');
            if($projectId){
                $project = Project::findOrFailByUuid($projectId);
                if ($project && !$project->is_procurement_enable) {
                    return false;
                }
            }
            
            if($user->hasRole($user::ROLE_DESIGNER)){
                return (bool)$user->can_procure && $user->hasPermissionTo('can procure');
            }
            return true;
        });
        
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return tenant_route('password.reset', ['token'=>$token])."?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
