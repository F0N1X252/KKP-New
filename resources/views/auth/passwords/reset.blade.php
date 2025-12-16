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
        background: radial-gradient(circle at 20% 20%, rgba(79, 70, 229, 0.15), transparent 40%),
                    radial-gradient(circle at 80% 20%, rgba(99, 102, 241, 0.1), transparent 40%),
                    radial-gradient(circle at 80% 80%, rgba(79, 70, 229, 0.15), transparent 40%),
                    radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.1), transparent 40%);
        background-color: var(--bg-light);
        animation: backgroundFloat 20s ease-in-out infinite;
    }

    @keyframes backgroundFloat {
        0%, 100% { transform: rotate(0deg) scale(1); }
        50% { transform: rotate(1deg) scale(1.02); }
    }

    [data-bs-theme="dark"] .bg-animation {
        background-color: var(--bg-dark);
        background-image: radial-gradient(circle at 50% 50%, rgba(79, 70, 229, 0.1), transparent 60%),
                         radial-gradient(circle at 30% 30%, rgba(99, 102, 241, 0.05), transparent 50%);
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
        animation: slideIn 0.6s ease-out;
    }

    @keyframes slideIn {
        from { 
            opacity: 0; 
            transform: translateY(30px) scale(0.95); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0) scale(1); 
        }
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
        transform: translateY(-1px);
    }
    [data-bs-theme="dark"] .form-control:focus {
        background: rgba(15, 23, 42, 0.8);
    }

    .form-control:read-only {
        background: rgba(0, 0, 0, 0.02);
        cursor: not-allowed;
    }
    [data-bs-theme="dark"] .form-control:read-only {
        background: rgba(255, 255, 255, 0.02);
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
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -10px rgba(79, 70, 229, 0.6);
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:active { 
        transform: translateY(0); 
    }

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
        transform: scale(1.1);
    }
    [data-bs-theme="dark"] .theme-toggle:hover { 
        background: rgba(255,255,255,0.1); 
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #94a3b8;
        z-index: 10;
        transition: color 0.2s;
    }

    .password-toggle:hover {
        color: var(--primary);
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
        background: rgba(79, 70, 229, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(79, 70, 229, 0.2);
        animation: logoFloat 3s ease-in-out infinite;
    }

    @keyframes logoFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
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

    .icon-header {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(99, 102, 241, 0.1));
        color: var(--primary);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin: 0 auto 1.5rem;
        border: 1px solid rgba(79, 70, 229, 0.2);
        animation: iconPulse 2s ease-in-out infinite;
    }

    @keyframes iconPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .back-link {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .back-link:hover { 
        text-decoration: none;
        color: var(--primary-hover);
        transform: translateX(-3px);
    }

    .back-link i {
        transition: transform 0.2s;
    }

    .back-link:hover i {
        transform: translateX(-2px);
    }

    /* Transitions */
    .fade-in { 
        animation: fadeIn 0.4s ease-out forwards; 
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Error states */
    .form-control.is-invalid {
        border-color: #ef4444;
        animation: shake 0.3s ease-in-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .text-danger {
        color: #ef4444 !important;
    }

    /* Loading state */
    .btn-loading {
        position: relative;
        pointer-events: none;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        top: 50%;
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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

        <!-- Logo -->
        <div class="text-center">
            <div class="brand-logo">
                <img src="{{ asset('images/icon-krealogi.png') }}" 
                     alt="PT. Krealogi Inovasi Digital" 
                     style="width: auto; height: 50px; max-width: 180px;">
            </div>
        </div>

        <!-- Reset Password Form -->
        <div class="fade-in">
            <div class="text-center mb-4">
                <div class="icon-header">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h4 class="fw-bold mb-1" style="color: var(--bs-body-color);">Reset Password</h4>
                <p class="text-muted small">Create your new secure password below</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 small mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" id="resetForm">
                @csrf
                
                <input name="token" value="{{ $token }}" type="hidden">

                <!-- Email (Read-only) -->
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input id="email" type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ $email ?? old('email') }}" 
                           required 
                           autocomplete="email" 
                           readonly>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <div class="position-relative">
                        <input id="password" type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               name="password" 
                               required 
                               placeholder="Enter new password"
                               autocomplete="new-password">
                        <i class="bi bi-eye password-toggle" onclick="togglePass('password', this)"></i>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="form-text small text-muted mt-1">
                        <i class="bi bi-info-circle me-1"></i>
                        Password must be at least 8 characters long
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label class="form-label">Confirm Password</label>
                    <div class="position-relative">
                        <input id="password-confirm" type="password" 
                               class="form-control" 
                               name="password_confirmation" 
                               required 
                               placeholder="Confirm new password"
                               autocomplete="new-password">
                        <i class="bi bi-eye password-toggle" onclick="togglePass('password-confirm', this)"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                    Reset Password <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-10">
                <a href="{{ route('login') }}" class="back-link">
                    <i class="bi bi-arrow-left"></i>
                    Back to Login
                </a>
            </div>
        </div>
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

    // Form Enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('resetForm');
        const submitBtn = document.getElementById('submitBtn');
        
        // Form submission loading state
        form.addEventListener('submit', function() {
            submitBtn.classList.add('btn-loading');
            submitBtn.innerHTML = '<span class="opacity-0">Reset Password</span>';
            submitBtn.disabled = true;
        });

        // Password confirmation validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password-confirm');
        
        function validatePasswordMatch() {
            if (confirmPassword.value && password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
                confirmPassword.classList.add('is-invalid');
            } else {
                confirmPassword.setCustomValidity('');
                confirmPassword.classList.remove('is-invalid');
            }
        }
        
        password.addEventListener('input', validatePasswordMatch);
        confirmPassword.addEventListener('input', validatePasswordMatch);

        // Auto-focus first input
        document.getElementById('password').focus();
    });
</script>
@endsection