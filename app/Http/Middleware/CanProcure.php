<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanProcure
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

    $projectId = $request->project ?? '';
    if($projectId){
        $project = Project::findOrFailByUuid($projectId);
        if ($project && !$project->is_procurement_enable) {
            abort(403, "Procurement is disabled for this project.");
        }
    }
    
        $user = auth()?->user();
        if ($user && $user->hasRole(User::ROLE_DESIGNER) ) {   
           
            
            abort_if((bool)$user?->can_procure === false && !$user->hasPermissionTo('can procure') , 403, "Permission denied.");
            if($projectId && $project->owner_id != auth()?->user()->id){
                abort_if(!in_array($user->id, $project->members->pluck('id')->toArray()), 403, "User not authorized to access this project.");

            }
        }
       
        return $next($request);
    }
}
