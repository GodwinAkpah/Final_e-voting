<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;


class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request,AuthService $authServices)
    {
        try {
            $unk_path = storage_path()."{$request->voter_no}.jpg";
            $k_path = $authServices->getUserPic($request->voter_no);
            
            $file = file_put_contents($unk_path ,base64_decode($request->unk_image));
            $isAuthentic = $authServices->isImageIdentical($k_path,$unk_path);

            //code...
            if (Auth::attempt($request->only('voter_no', 'password')) && $isAuthentic) {
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
