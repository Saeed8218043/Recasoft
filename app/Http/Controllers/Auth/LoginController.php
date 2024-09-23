<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use DB;
use Hash;
use Mail;

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
    //protected $redirectTo = RouteServiceProvider::HOME;

    protected function redirectTo()
    {
        $user = Auth::user()->id;
        $user_Role = \App\CompanyMembers::where('user_id', $user)->first();
        if (isset($user_Role) && $user_Role->role == 2) {
            $companyId = $user_Role->company_id; 
            return route('equipments', ['company_id' => $companyId]);

        } else {
            return RouteServiceProvider::HOME;
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /*public function authenticated(Request $request, $user)
    {
        //
    }*/
      
    // public function loginHandle(){
    //     if(Auth::user()){
    //         $redirectTo = RouteServiceProvider::HOME;
    //         return redirect($redirectTo);
    //     }else{
    //         return route('login');
    //     }
    // }

    public function forgotPassword(Request $request){
        return view('auth.forgot-password');
    }

    public function forgotPasswordSend(Request $request){
        $email = isset($request->email)?$request->email:'';
        $query = DB::select("select * from users where email=? limit 1",array($email));
        if($query){
            $subject = 'Forgot Password';
            $to_name = isset($query[0]->name)?$query[0]->name:'';
            $to_email = isset($query[0]->email)?$query[0]->email:'';
            $data_ar = ['name'=>$to_name];
            $url = url('reset-password/'.\Crypt::encrypt($query[0]->id));
            if($url!=''){
                $data_ar['url']=$url;
            }
            Mail::send('emails.reset-password', $data_ar, function($message) use ($to_name, $to_email, $subject) {
                $message->to($to_email, $to_name)
                ->subject($subject);
                 $message->from('notification@recasoft.com','Recasoft Technologies');
                 $message->replyTo('notification@recasoft.com','Recasoft Technologies');
                // $message->from('notification@app.recasoft.no','Recasoft Technologies');
                //  $message->replyTo('notification@app.recasoft.no','Recasoft Technologies');
            });
            $message = 'Forgot password email has been sent to '.$email.' email address. Please check your mailbox';
            return redirect(url('login'))->with('message',$message);
        }else{
            $message = 'Invalid email address';
            return redirect(url('forgot-password'))->with('error',$message);
        }
    }

    public function resetPassword(Request $request){
        $id = isset($request->id)?$request->id:'';
        $userId = \Crypt::decrypt($id);
        $user = DB::select("select * from users where id=? limit 1",array($userId));
        return view('auth.reset-password',compact('user','id'));
    }

    public function resetPasswordUpdate(Request $request){
        $id = isset($request->id)?$request->id:'';
        $userId = \Crypt::decrypt($id);
        $password = isset($request->password)?$request->password:'';
        $confirm_password = isset($request->confirm_password)?$request->confirm_password:'';
        if($password==$confirm_password){
            DB::table('users')->where('id',$userId)->update(array('password'=>Hash::make($password)));
            $thankyou = ['success'=>'Your password has been changed successfully'];
            return view('auth.thankyou',$thankyou);
            // return redirect(url('login'))->with('message','Your password has been changed successfully');
        }else{
            return redirect(url('reset-password/'.$id))->with('error','Password and confirm password should be same');
        }
    }

    public function abc(){
        return view('emails.reset-password');
    }
}
