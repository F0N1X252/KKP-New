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
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 24px;
        padding: 3rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        max-width: 500px;
        width: 100%;
    }

    [data-bs-theme="dark"] .auth-card {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text-dark);
    }

    .verification-icon {
        font-size: 4rem;
        color: var(--primary);
        margin-bottom: 1.5rem;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 60%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        80% { transform: translateY(-5px); }
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), #6366f1);
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-transform: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-hover), #5b5fcf);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
    }

    .btn-outline-primary {
        border: 2px solid var(--primary);
        color: var(--primary);
        border-radius: 12px;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
    }

    .alert {
        border: none;
        border-radius: 12px;
        font-weight: 500;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.1);
        color: #059669;
        border: 1px solid rgba(34, 197, 94, 0.2);
    }

    .alert-info {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
</style>
@endsection

@section('content')
<div class="bg-animation"></div>

<div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
    <div class="auth-card text-center">
        <div class="verification-icon">
            <i class="bi bi-envelope-check"></i>
        </div>
        
        <h2 class="fw-bold mb-3">Verify Your Email</h2>
        <p class="text-muted mb-4">
            We've sent a verification link to your email address. Please check your inbox and click the link to verify your account.
        </p>

        @if (session('resent'))
            <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                A fresh verification link has been sent to your email address.
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                {{ session('status') }}
            </div>
        @endif

        <div class="d-grid gap-3">
            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-arrow-clockwise me-2"></i>
                    Resend Verification Email
                </button>
            </form>

            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="btn btn-outline-primary">
                <i class="bi bi-box-arrow-left me-2"></i>
                Sign Out
            </a>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

        <div class="mt-4">
            <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Your account is secured with email verification
            </small>
        </div>
    </div>
</div>
@endsection
