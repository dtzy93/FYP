<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Auth;
use App\Models\User;
use App\Mail\ForgotPasswordMail;
use Mail;
use Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        // dd(Hash::make(123456));
        if (!empty(Auth::check())) {
            if (Auth::user()->user_type == 1) {
                return redirect('admin/dashboard');
            } else if (Auth::user()->user_type == 2) {
                return redirect('teacher/dashboard');
            } else if (Auth::user()->user_type == 3) {
                return redirect('student/dashboard');
            } else if (Auth::user()->user_type == 4) {
                return redirect('parent/dashboard');
            }
        }
        return view('auth.login');
    }

    public function AuthLogin(Request $request)
    {
        $remember = !empty($request->remember) ? true : false;

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            if (Auth::user()->user_type == 1) {
                return redirect('admin/dashboard');
            } else if (Auth::user()->user_type == 2) {
                return redirect('teacher/dashboard');
            } else if (Auth::user()->user_type == 3) {
                return redirect('student/dashboard');
            } else if (Auth::user()->user_type == 4) {
                return redirect('parent/dashboard');
            }

        } else {
            return redirect()->back()->with('error', 'Please enter correct email and password');
        }

    }

    public function forgotpassword(){
        return view('auth.forgot');
    }

    public function PostForgotPassword(Request $request)
{
    $user = User::getEmailSingle($request->email);

    if (!empty($user)) {
        $user->remember_token = Str::random(30);
        $user->save();

        Mail::to($user->email)->send(new ForgotPasswordMail($user));

        return redirect()->back()->with('success', "Please check your email and reset your password.");
    } else {
        return redirect()->back()->with('error', "Email not found in the system.");
    }
}

    public function reset($remember_token){
        $user = User::getTokenSingle($remember_token);
        if(!empty($user)){
            $data['user'] = $user;
            return view('auth.reset');
        }
        else{
            abort(404);
        }
    }

    public function PostReset($token, Request $request)
{
    // Define custom error messages for validation rules
    $messages = [
        'password.min' => 'Password must be at least :min characters long.',
        'password.regex' => 'Password must contain at least one alphabet and one special symbol.',
    ];

    // Validate the request data
    $validator = Validator::make($request->all(), [
        'password' => [
            'required',
            'string',
            'min:8', // Minimum length of 8 characters
            'confirmed', // Password and confirm password should match
            'regex:/[a-zA-Z]/', // At least one alphabet
            'regex:/[!@#$%^&*(),.?":{}|<>_]/', // At least one special symbol
        ],
    ], $messages);

    // Check if validation fails
    if ($validator->fails()) {
        // Get the validation errors
        $errors = $validator->errors();
        
        // Extract the errors related to the 'password' field
        $passwordErrors = $errors->get('password');

        // Redirect back with the password errors and input data
        return redirect()->back()->withErrors($validator)->withInput()->with('passwordErrors', $passwordErrors);
    }

    // Find the user by token
    $user = User::getTokenSingle($token);

    // Check if user exists
    if ($user) {
        // Update user's password and token
        $user->password = Hash::make($request->password);
        $user->remember_token = Str::random(30);
        $user->save();

        // Redirect to the root URL with a success message
        return redirect('/')->with('success', 'Password successfully reset');
    } else {
        // Handle the case where no user is found with the provided token
        return redirect()->back()->with('error', 'Invalid token or user not found');
    }
}


public function logout(Request $request)
{
    Auth::logout();
    $request->session()->flash('success', 'You have been successfully logged out.');
    return redirect(url('/'));
}

}
