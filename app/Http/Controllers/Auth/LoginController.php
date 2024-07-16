<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout',
            'locked',
            'unlock'
        ]);
    }

    public function locked(Request $request)
    {
        $sessionKeyLockScreen = $this->getSessionKeyLockScreen();
        $urlPrevious = session()->get($sessionKeyLockScreen) ?? url()->previous();
        if(Auth::check()){
            session()->put('locked', true);
            if($urlPrevious != route('locked') && !empty($urlPrevious) ){
                session()->put($sessionKeyLockScreen, $urlPrevious);
            }

            return view('auth.locked');
        }
        
        return redirect(route('login'));
    }

    public function unlock(Request $request)
    {
        $sessionKeyLockScreen = $this->getSessionKeyLockScreen();
        $check = Hash::check($request->input('password'), $request->user()->password);
        if(!$check){
            return redirect()->route('locked')->withErrors(['Your password does not match your profile.']);
        }
        $redirect = session()->get($sessionKeyLockScreen) ?? route('admin.dashboard');
        session()->forget(['locked', $sessionKeyLockScreen]);

        return redirect()->to($redirect);
    }

    /**
     * Get seesion key lock screen
     * 
     * @return string
     */
    public function getSessionKeyLockScreen()
    {
        return 'lock_screen_redirect_' . Auth::user()->id;
    }
}
