<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

## routes/web.php

    Route::post('login', 'Auth\LoginController@login')->name('login');
    Route::post('sendOtp', 'Auth\LoginController@sendOtp')->name('sendOtp');

##  app/Http/Controllers/Auth/LoginController.php

** Name Space

    use Illuminate\Http\Request;
    use App\User;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Redirect;


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

--otp function

        public function sendOtp(Request $request){

        $otp = rand(1000,9999);
        Log::info("otp = ".$otp);
        $user = User::where('mobile','=',$request->mobile)->update(['otp' => $otp]);
        // send otp to mobile no using sms api
        return response()->json([$user],200);
    }


## app/Http/Controllers/Auth/RegisterController.php MOD

     return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['required', 'numeric', 'max:9999999999', 'unique:users'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);


     protected function create(array $data)
    {

        // echo "<pre>";
        // print_r($data['mobile']);
        // exit;
        return User::create([
            'name' => $data['name'],
            // 'email' => $data['email'],
            'email' => 'ph'.$data['mobile'].'@gmail.com',
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
        ]);
    }



## app/User.php

    protected $fillable = [
        'name', 'email', 'password','mobile','otp'
    ];


make in view files also


The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# laravel_custome-login
