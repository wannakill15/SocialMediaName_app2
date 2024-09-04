<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(){
        return view('auth.login');
    }

    public function register(){
        if(session()->has('loggedInUser')){
            return redirect('/profile');
        }else{
            return view('auth.register');
        }
    }

    public function forgot(){
        return view('auth.forgot');
    }

    public function reset(){
        return view('auth.reset');
    }

    //handle register user ajax request
    public function saveUser(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:100',
            'password' => 'required|min:6|max:50',
            'cpassword' => "required|min:6|same:password"
        ],[
            'cpassword.same' => 'Password did not match! ',
            'cpassword.required' => 'Confirm Password is required! '
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'messages' => $validator->getMessageBag()
            ]);
        }else{
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json([
                'status' => 200,
                'messages' => 'Registered Successfully!'
            ]);
        }
    }

    // handle login
    public function loginUser(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
            'password' => 'required|min:6|max:50'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 400,
                'messages' => $validator->getMessageBag()
            ]);
        }else{
            $user = User::where('email', $request->email)->first();
            if($user){
                if(Hash::check($request->password, $user->password)){
                    $request->session()->put('loggedInUser', $user->id);
                    return response()->json([
                        'status' => 200,
                        'messages' => 'success'
                    ]);
                }else{
                    return response()->json([
                        'status' => 401,
                        'messages' => 'E-mail or password is incorrect'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 401,
                    'messages' => 'User not found!'
                ]);
            }
        }
    }

    //profile page
    public function profile(){
        $data = ['userInfo' => DB::table('users')->where('id', session('loggedInUser'))->first()];
        return view('profile', $data);
    }
    
    //logout method
    public function logout(){
        if(session()->has('loggedInUser')){
            session()->pull('loggedInUser');
            return redirect('/');
        }
    }


    // handlle update user profile image ajax request
    public function profileImageUpdate(Request $request){
        $user_id = $request->user_id;
        $user = User:: find ($user_id);

        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/img/', $fileName);

            if($user->picture){
                Storage::delete('public/img/' . $user->picture);
            }
        }

        User::where('id', $user_id)->update([
            'picture' => $fileName
        ]);

        return response()->json([
            'status' => 200,
            'messages' => 'Profile image updated Successfully! '
        ]);
    }

    //handle profile update ajax request
    public function profileUpdate (Request $request){
        User::where('id', $request->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'phone' => $request->phone
        ]);
        return response()->json([
            'status' => 200,
            'messages' => 'Profile updated Successfully!'
        ]);
    }

    // handle forgot password ajaz request
    public function forgotPassword(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|max:100'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'messages' => $validator->getMessageBag()
            ]);
        } else {
            $token = Str::uuid();
            $user = DB::table('users')->where('email', $request->email)-> first();
            $details = [
                'body' => route('reset', ['email'=> $request->email, 'token' => $token])
            ];

            if($user){
                User::where('email', $request->email)->update([
                    'token' => $token,
                    'token_expire' => Carbon::now()->addMinutes(10)->toDateString()
                ]);
                
                Mail::to ($request->email)->send(new ForgotPassword($details));
                return response()->json([
                    'status' => 200,
                    'messages' => 'Reset password link has been sent to your E-mail'
                ]);
            }else{ 
                return response()->json([
                    'status' => 401,
                    'messages' => 'This E-mail is not register!'
                ]);
            }
        }
    }
}


