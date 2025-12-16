<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/email/verify';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function showRegistrationForm()
    {
        return view('auth.login');
    }

    // Custom register method with email verification
    public function register(Request $request)
    {
        // 1. Validasi Input
        $this->validator($request->all())->validate();

        // 2. Buat User Baru
        event(new Registered($user = $this->create($request->all())));

        // 3. Login user otomatis tapi arahkan ke halaman verifikasi
        $this->guard()->login($user);

        // 4. Redirect ke halaman email verification
        return redirect()->route('verification.notice')
            ->with('status', 'Registration successful! Please check your email and verify your account before proceeding.');
    }
}