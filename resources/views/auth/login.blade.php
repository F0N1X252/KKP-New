@extends('layouts.auth')

@section('styles')
<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --bg-dark: #0f172a;
        --bg-light: #f8fafc;
        --text-dark: #f8fafc;
        --text-light: #1e293b;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        overflow: hidden;
        margin: 0;
        transition: background 0.5s ease;
    }

    /* === ANIMATED BACKGROUND === */
    .bg-animation {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: -1;
        background: radial-gradient(circle at 0% 0%, rgba(79, 70, 229, 0.15), transparent 40%),
                    radial-gradient(circle at 100% 0%, rgba(99, 102, 241, 0.1), transparent 40%),
                    radial-gradient(circle at 100% 100%, rgba(79, 70, 229, 0.15), transparent 40%),
                    radial-gradient(circle at 0% 100%, rgba(99, 102, 241, 0.1), transparent 40%);
        background-color: var(--bg-light);
    }

    [data-bs-theme="dark"] .bg-animation {
        background-color: var(--bg-dark);
        background-image: radial-gradient(circle at 50% 50%, rgba(79, 70, 229, 0.1), transparent 60%);
    }

    /* === GLASS CARD === */
    .auth-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        padding: 3rem 2.5rem;
        width: 100%;
        max-width: 450px;
        position: relative;
        transition: all 0.3s ease;
    }

    [data-bs-theme="dark"] .auth-card {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    /* === FORM ELEMENTS === */
    .form-label {
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        color: var(--text-light);
    }
    [data-bs-theme="dark"] .form-label { color: #cbd5e1; }

    .form-control {
        padding: 0.875rem 1rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: rgba(255, 255, 255, 0.5);
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    
    [data-bs-theme="dark"] .form-control {
        background: rgba(15, 23, 42, 0.5);
        border-color: #334155;
        color: white;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        background: #fff;
    }
    [data-bs-theme="dark"] .form-control:focus {
        background: rgba(15, 23, 42, 0.8);
    }

    /* === BUTTONS === */
    .btn-primary {
        background: linear-gradient(135deg, #4f46e5, #4338ca);
        border: none;
        padding: 0.875rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        letter-spacing: 0.3px;
        box-shadow: 0 10px 20px -10px rgba(79, 70, 229, 0.5);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -10px rgba(79, 70, 229, 0.6);
    }

    .btn-primary:active { transform: translateY(0); }

    /* === TOGGLES === */
    .theme-toggle {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #64748b;
        background: transparent;
        border: 1px solid transparent;
        transition: 0.3s;
    }
    .theme-toggle:hover {
        background: rgba(0,0,0,0.05);
        color: var(--primary);
    }
    [data-bs-theme="dark"] .theme-toggle:hover { background: rgba(255,255,255,0.1); }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #94a3b8;
        z-index: 10;
    }

    /* === UTILS === */
    .auth-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 1.5rem;
    }

    .brand-logo {
        width: auto;
        height: 80px;
        max-width: 200px;
        margin: 0 auto 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        padding: 15px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .brand-logo img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: brightness(1.1) contrast(1.05);
    }

    [data-bs-theme="dark"] .brand-logo {
        background: rgba(30, 41, 59, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .switch-link {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }
    .switch-link:hover { text-decoration: underline; }

    /* Transitions */
    .fade-in { animation: fadeIn 0.4s ease-out forwards; }
    .hidden { display: none; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="bg-animation"></div>

<div class="auth-container">
    <div class="auth-card">
        <!-- Theme Toggle -->
        <div class="theme-toggle" id="themeBtn" title="Switch Theme">
            <i class="bi bi-moon-stars-fill"></i>
        </div>

        <!-- Updated Logo Section -->
        <div class="text-center">
            <div class="brand-logo">
                <img src="{{ asset('images/icon-krealogi.png') }}" 
                     alt="PT. Krealogi Inovasi Digital" 
                     style="width: auto; height: 50px; max-width: 180px;">
            </div>
        </div>

        <!-- LOGIN FORM -->
        <div id="login-section" class="fade-in">
            <div class="text-center mb-4">
                <h4 class="fw-bold mb-1" style="color: var(--bs-body-color);">CRM Krealogi Inovasi Digital</h4>
                <p class="text-muted small">Please enter your details to sign in</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 small mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">{{ trans('global.login_email') }}</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           placeholder="name@company.com" 
                           value="{{ session('registered_email') ? session('registered_email') : old('email') }}" 
                           required autofocus>
                    @error('email') 
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0">{{ trans('global.login_password') }}</label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="small text-decoration-none" style="color: var(--primary);">
                                {{ trans('global.forgot_password') }}
                            </a>
                        @endif
                    </div>
                    <div class="position-relative">
                        <input type="password" name="password" id="loginPass" class="form-control @error('password') is-invalid @enderror" 
                               placeholder="••••••••" 
                               value="{{ session('registered_password') ? session('registered_password') : '' }}"
                               required>
                        <i class="bi bi-eye password-toggle" onclick="togglePass('loginPass', this)"></i>
                    </div>
                    @error('password') 
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" style="cursor: pointer;">
                        <label class="form-check-label text-muted small" for="remember" style="cursor: pointer;">
                            {{ trans('global.remember_me') }}
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    {{ trans('global.login') }} <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>

            @if(Route::has('register'))
            <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-10">
                <p class="text-muted small mb-0">
                    Don't have an account? 
                    <span class="switch-link ms-1" onclick="switchForm('register')">Create Account</span>
                </p>
            </div>
            @endif
        </div>

        <!-- REGISTER FORM -->
        @if(Route::has('register'))
        <div id="register-section" class="hidden">
            <div class="text-center mb-4">
                <h4 class="fw-bold mb-1" style="color: var(--bs-body-color);">Create Account</h4>
                <p class="text-muted small">Start your journey with us today</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                           placeholder="John Doe" value="{{ old('name') }}" required>
                    @error('name') 
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           placeholder="name@company.com" value="{{ old('email') }}" required>
                    @error('email') 
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="position-relative">
                        <input type="password" name="password" id="regPass" class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Min. 8 characters" required>
                        <i class="bi bi-eye password-toggle" onclick="togglePass('regPass', this)"></i>
                    </div>
                    @error('password') 
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirm Password</label>
                    <div class="position-relative">
                        <input type="password" name="password_confirmation" id="regConfirmPass" class="form-control" 
                               placeholder="Repeat password" required>
                        <i class="bi bi-eye password-toggle" onclick="togglePass('regConfirmPass', this)"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Sign Up <i class="bi bi-person-plus ms-2"></i>
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-10">
                <p class="text-muted small mb-0">
                    Already have an account? 
                    <span class="switch-link ms-1" onclick="switchForm('login')">Sign In</span>
                </p>
            </div>
        </div>
        @endif

    </div>
    
    <div class="text-center position-absolute bottom-0 py-3 text-muted small opacity-50">
        &copy; {{ date('Y') }} {{ trans('panel.site_title') }}. All rights reserved.
    </div>
</div>

<script>
    // Theme Logic
    const htmlEl = document.documentElement;
    const themeBtn = document.getElementById('themeBtn');
    const icon = themeBtn.querySelector('i');
    
    function setTheme(theme) {
        htmlEl.setAttribute('data-bs-theme', theme);
        localStorage.setItem('theme', theme);
        
        if (theme === 'light') {
            icon.className = 'bi bi-moon-stars-fill';
        } else {
            icon.className = 'bi bi-sun-fill';
        }
    }

    themeBtn.addEventListener('click', () => {
        const current = localStorage.getItem('theme') || 'light';
        setTheme(current === 'light' ? 'dark' : 'light');
    });

    // Init Theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

    // Password Toggle
    function togglePass(id, el) {
        const input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
            el.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            el.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    // Switch Form Animation
    function switchForm(target) {
        const loginSec = document.getElementById('login-section');
        const regSec = document.getElementById('register-section');

        if(target === 'register') {
            loginSec.classList.add('hidden');
            loginSec.classList.remove('fade-in');
            regSec.classList.remove('hidden');
            regSec.classList.add('fade-in');
        } else {
            regSec.classList.add('hidden');
            regSec.classList.remove('fade-in');
            loginSec.classList.remove('hidden');
            loginSec.classList.add('fade-in');
        }
    }

    // Auto-Handle Errors
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->has('name') || $errors->has('password_confirmation'))
            switchForm('register');
        @elseif(session('auto_switch_login'))
            switchForm('login');
        @endif
    });
</script>
@endsection