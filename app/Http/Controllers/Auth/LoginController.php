<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

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

    // public function username(){
    //     return 'mobile';
    // }

    public function login(Request $request){
        Log::info($request);
        if(Auth::attempt(['mobile' => request('mobile'), 'password' => request('password')])){
            return redirect($this->redirectTo);
        }
        else{

            $user  = User::where([['mobile','=',request('mobile')],['otp','=',request('otp')]])->first();
            if( $user && request('otp')){
                Auth::login($user, true);
                User::where('mobile','=',$request->mobile)->update(['otp' => null]);
                return redirect($this->redirectTo);
            }

            return Redirect::back ();
        }
    }

    public function sendOtp(Request $request){

        $otp = rand(1000,9999);
        Log::info("otp = ".$otp);
        $user = User::where('mobile','=',$request->mobile)->update(['otp' => $otp]);
        // send otp to mobile no using sms api
        return response()->json([$user],200);
    }
    
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
        $this->middleware('guest')->except('logout');
    }
}
