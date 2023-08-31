<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;


class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $path = storage_path();

        $file = file_put_contents($path.'/test.png',base64_decode($request->unk_image));
        dd($file);
        try {
            //code...
            if (Auth::attempt($request->only('voter_no', 'password'))) {
                $user = Auth::user();
                // dd($user);
                if($user->hasRole('admin')){
                    return redirect()->route('dashboard');
                }elseif ($user->hasRole('voter')){
                    return redirect()->route('dashboard');
                }
            }elseif(User::where('voter_no', $request->only('voter_no'))->doesntExist()){
                return redirect()->route('login')->withErrors('Account Does not exist');
            } else {
                return redirect()->route('login')->withErrors('Invalid Credentials');
            }
        } catch (Exception $e) {
            return back()->withError($e);
        }
        // dd($request->all());

    }

    // function to logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
