<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantRestriction
{
    private $shouldRedirectToLogin = false;
    private $redirectMessage = '';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if the user is logged in
        if ($user) {
            // logout if password has changed
            $this->checkPasswordChanged($user, $request);

            // logout if company has not activated
            $this->checkCompanyStatus($user, $request);

            if ($this->shouldRedirectToLogin) {
                return redirect(tenant_route('login'))->with('message', $this->redirectMessage);
            }
        }

        return $next($request);
    }

    private function checkPasswordChanged($user, $request)
    {
        // Check if password_changed is true
        if ($user->password_changed) {

            $user->password_changed = false;
            $user->save();

            $this->redirectMessage = 'Your password has been changed. Please log in again.';
            $this->logoutCall($request);
        }
    }

    private function checkCompanyStatus($user, $request)
    {
        if ($user && $user?->company?->status != Company::STATUS_ACTIVE) {
            $this->redirectMessage = 'You don\'t have access. Please contact support for assistance.';
            $this->logoutCall($request);
        }
    }

    private function logoutCall($request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $this->shouldRedirectToLogin = true;

    }
}
