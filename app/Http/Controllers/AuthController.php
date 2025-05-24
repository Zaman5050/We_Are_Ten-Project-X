<?php 

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\PasswordResetRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules;
use App\Traits\AuthExtension;

class AuthController extends Controller
{
    use AuthExtension;
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('super-admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('home'));
    }


    public function sendResetLinkEmail(PasswordResetRequest $request)
    {
        // $this->checkUserRole($request->email);
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }


    public function resetPassword(Request $request)
    {
        $this->checkUserRole($request->email);
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);       

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('home')->with('status', __($status))
                    : back()->withErrors(['password' => [__($status)]]);
    }
        /*
            Tenant routes
        */

    public function tenantLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // check the current user compapny  status is active before login attempted
        $this->checkCompanyIsActive($request->email);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function tenantLogout(Request $request)
    {
      
        $user = Auth::user();
        if ($user) {
            User::where('id', auth()->id())->update([
                'dropbox_access_token' => null,
                'dropbox_refresh_token' => null
            ]);
        }       
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(tenant_route('home'));
    }
}