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
        --success: #10b981;
        --success-hover: #059669;
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
        background: radial-gradient(circle at 0% 0%, rgba(16, 185, 129, 0.15), transparent 40%),
                    radial-gradient(circle at 100% 0%, rgba(79, 70, 229, 0.1), transparent 40%),
                    radial-gradient(circle at 100% 100%, rgba(16, 185, 129, 0.15), transparent 40%),
                    radial-gradient(circle at 0% 100%, rgba(79, 70, 229, 0.1), transparent 40%);
        background-color: var(--bg-light);
    }

    [data-bs-theme="dark"] .bg-animation {
        background-color: var(--bg-dark);
        background-image: radial-gradient(circle at 50% 50%, rgba(16, 185, 129, 0.1), transparent 60%);
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
        max-width: 600px;
        width: 100%;
        text-align: center;
    }

    [data-bs-theme="dark"] .auth-card {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text-dark);
    }

    .success-icon {
        font-size: 5rem;
        color: var(--success);
        margin-bottom: 1.5rem;
        animation: successPulse 2s infinite;
    }

    @keyframes successPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .checkmark {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--success), #34d399);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        animation: checkmarkShow 0.8s ease-in-out;
    }

    @keyframes checkmarkShow {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); opacity: 1; }
    }

    .checkmark i {
        font-size: 3rem;
        color: white;
        animation: checkmarkBounce 0.6s 0.8s ease-in-out;
    }

    @keyframes checkmarkBounce {
        0% { transform: scale(0); }
        60% { transform: scale(1.3); }
        100% { transform: scale(1); }
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), #6366f1);
        border: none;
        border-radius: 12px;
        padding: 12px 32px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-transform: none;
        margin: 0 10px;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-hover), #5b5fcf);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success), #34d399);
        border: none;
        border-radius: 12px;
        padding: 12px 32px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white;
        margin: 0 10px;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, var(--success-hover), #10b981);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .countdown {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--primary);
        margin-top: 1rem;
    }

    .user-info {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 12px;
        padding: 1rem;
        margin: 1.5rem 0;
    }

    .confetti {
        position: fixed;
        width: 10px;
        height: 10px;
        background: #f39c12;
        z-index: 1000;
        animation: confetti-fall 3s linear infinite;
    }

    @keyframes confetti-fall {
        0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
        100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
    }
</style>
@endsection

@section('content')
<div class="bg-animation"></div>

<div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
    <div class="auth-card">
        <!-- Success Animation -->
        <div class="checkmark">
            <i class="bi bi-check-lg"></i>
        </div>
        
        <h1 class="fw-bold mb-3 text-success">🎉 Email Verified Successfully!</h1>
        
        <div class="user-info">
            <h5 class="mb-1">Welcome, {{ $user->name }}!</h5>
            <p class="text-muted mb-0">{{ $user->email }}</p>
        </div>

        <p class="lead mb-4">{{ $message }}</p>
        
        <p class="text-muted mb-4">
            Your account is now fully activated and ready to use. You can now access all features of the Support Ticketing System.
        </p>

        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3">
            <a href="{{ $loginUrl }}" class="btn btn-success">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Sign In Now
            </a>
            
            <a href="{{ route('admin.home') }}" class="btn btn-primary">
                <i class="bi bi-speedometer2 me-2"></i>
                Go to Dashboard
            </a>
        </div>

        <div class="countdown mt-4">
            <i class="bi bi-clock me-2"></i>
            Auto-redirecting to login in <span id="countdown">10</span> seconds...
        </div>

        <div class="mt-4">
            <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Your email verification was completed securely
            </small>
        </div>
    </div>
</div>

<!-- Confetti Animation -->
<script>
    // Create confetti effect
    function createConfetti() {
        const colors = ['#f39c12', '#e74c3c', '#3498db', '#2ecc71', '#9b59b6', '#f1c40f'];
        
        for (let i = 0; i < 50; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDelay = Math.random() * 2 + 's';
                confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
                
                document.body.appendChild(confetti);
                
                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            }, i * 100);
        }
    }

    // Countdown timer
    let countdown = 10;
    const countdownElement = document.getElementById('countdown');
    
    const timer = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(timer);
            window.location.href = '{{ $loginUrl }}';
        }
    }, 1000);

    // Start confetti animation
    createConfetti();
    
    // Add sound effect (optional)
    document.addEventListener('DOMContentLoaded', function() {
        // You can add a success sound here if needed
        console.log('🎉 Email verification completed successfully!');
    });
</script>
@endsection