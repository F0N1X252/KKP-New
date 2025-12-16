<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $apiToken = '';
        if (auth()->check()) {
            $apiToken = \App\Helpers\ApiTokenHelper::generateTokenForUser(auth()->user()) ?? '';
        }
    @endphp
    <meta name="api-token" content="{{ $apiToken }}">
    
    <title>{{ trans('panel.site_title') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon-krealogi.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/icon-krealogi.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons & CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- Lottie Player (Resource Aman & Modern untuk Animasi) -->
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>

    <!-- Scripts Core -->
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>

    @yield('styles')
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --secondary-color: #6b7280;
            --light-color: #f8fafc;
            --dark-color: #1f2937;
            --border-color: #e5e7eb;
            --text-muted: #6b7280;
            --sidebar-width: 260px;
            --navbar-height: 70px;
            --sidebar-bg: #0f172a; 
        }

        [data-bs-theme="dark"] {
            --primary-color: #6366f1;
            --dark-color: #f8fafc;
            --light-color: #0f172a;
            --text-muted: #94a3b8;
            --border-color: #374151;
            --sidebar-bg: #020617;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; outline: none !important; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 14px;
            color: var(--dark-color);
            background-color: var(--light-color);
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .admin-wrapper { display: flex; min-height: 100vh; position: relative; }

        /* --- Sidebar Styles --- */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: white;
            position: fixed;
            top: 0; left: 0; height: 100vh;
            z-index: 1050;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
            display: flex; flex-direction: column;
        }

        .sidebar-header {
            padding: 0 1.5rem;
            height: var(--navbar-height);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center;
            background: rgba(0,0,0,0.1);
            flex-shrink: 0;
        }

        .sidebar-brand {
            font-size: 1.1rem; font-weight: 800; color: white;
            text-decoration: none; display: flex; align-items: center; gap: 10px;
            letter-spacing: 0.5px;
        }

        .sidebar-menu { 
            padding: 10px 0; 
            list-style: none; 
            flex-grow: 1; 
            overflow-y: auto;
        }
        
        .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }

        /* --- LOTTIE FUN ZONE (Area Bot) --- */
        .sidebar-fun-zone {
            padding: 20px;
            background: linear-gradient(180deg, rgba(15,23,42,0) 0%, rgba(0,0,0,0.6) 100%);
            text-align: center;
            margin-top: auto;
            flex-shrink: 0;
            position: relative;
            cursor: pointer;
        }

        /* Container Animasi */
        .lottie-container {
            width: 140px; height: 140px; /* Ukuran pas */
            margin: -20px auto 0; 
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            filter: drop-shadow(0 0 20px rgba(79, 70, 229, 0.3)); /* Efek Glow Neon */
        }

        .lottie-container:hover { transform: scale(1.1) rotate(5deg); }
        .lottie-container:active { transform: scale(0.95); }

        /* Bubble Chat Lucu */
        .chat-bubble {
            background: #fff;
            color: #1e293b;
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            position: absolute;
            top: 10px; right: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            opacity: 0;
            transform: translateY(10px) scale(0.8);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            pointer-events: none;
            z-index: 10;
            max-width: 140px;
        }
        
        .chat-bubble::after {
            content: ''; position: absolute;
            bottom: -6px; left: 20px;
            width: 0; height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid #fff;
        }

        .chat-bubble.show { opacity: 1; transform: translateY(-15px) scale(1); }

        .bot-name {
            font-size: 0.8rem; font-weight: 700; color: rgba(255,255,255,0.9);
            margin-top: -15px; position: relative; z-index: 2;
        }
        .bot-status {
            font-size: 0.65rem; color: #94a3b8;
            font-family: monospace; display: block;
        }

        /* --- Main Content --- */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1; min-height: 100vh;
            display: flex; flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        .admin-navbar {
            height: var(--navbar-height);
            background: var(--light-color);
            border-bottom: 1px solid var(--border-color);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 30px;
            position: sticky; top: 0; z-index: 999;
            backdrop-filter: blur(8px);
        }
        [data-bs-theme="dark"] .admin-navbar { background: #1e293b; }

        .sidebar-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5); z-index: 1040;
            opacity: 0; visibility: hidden; transition: all 0.3s ease;
            backdrop-filter: blur(2px);
        }

        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-overlay.show { opacity: 1; visibility: visible; }
            .admin-navbar { padding: 0 15px; }
        }

        .theme-toggle {
            background: transparent; border: 1px solid var(--border-color);
            color: var(--text-muted); padding: 8px; border-radius: 8px;
            transition: all 0.2s;
        }
        .theme-toggle:hover { color: var(--primary-color); background: rgba(79, 70, 229, 0.1); }
        
        .avatar-initial {
            width: 38px; height: 38px;
            background: var(--primary-color); color: white;
            border-radius: 8px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        
        .content-area { flex: 1; padding: 30px; }
        @media(max-width: 768px) { .content-area { padding: 15px; } }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.home') }}" class="sidebar-brand">
                    <img src="{{ asset('images/icon-krealogi.png') }}" alt="Logo" class="me-2" style="width: 40px; height: auto;">
                    {{ strtoupper(trans('panel.site_title')) }}
                </a>
            </div>

            <div class="sidebar-menu">
                @include('partials.menu')
            </div>
            
            <!-- Modern Lottie Bot Zone -->
            <div class="sidebar-fun-zone" onclick="interactWithBot()">
                <!-- Bubble Chat -->
                <div class="chat-bubble" id="botBubble">Hello!</div>
                
                <!-- Animasi Robot (Aman & Ringan) -->
                <!-- URL ini PUBLIK dan STABIL dari LottieFiles Assets CDN -->
                <div class="lottie-container">
                    <dotlottie-player 
                        id="botPlayer"
                        src="https://assets5.lottiefiles.com/packages/lf20_S6vWEd.json" 
                        background="transparent" 
                        speed="1" 
                        loop 
                        autoplay>
                    </dotlottie-player>
                </div>

                <div class="bot-name">Helper Bot</div>
                <span class="bot-status">System Online</span>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Navbar -->
            <nav class="admin-navbar">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link text-body p-0 me-3 d-lg-none" onclick="toggleSidebar()">
                        <i class="bi bi-list fs-3"></i>
                    </button>
                    <h5 class="mb-0 fw-bold d-none d-sm-block text-truncate">@yield('page-title', 'Dashboard')</h5>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <button class="theme-toggle" onclick="toggleTheme()" title="Switch Theme">
                        <i class="bi bi-moon-stars" id="theme-icon"></i>
                    </button>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <div class="text-end me-3 d-none d-md-block">
                                <div class="fw-bold small lh-1">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ auth()->user()->email }}</div>
                            </div>
                            <div class="avatar-initial shadow-sm">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 p-2">
                            <li><h6 class="dropdown-header">Logged in as <strong>{{ auth()->user()->name }}</strong></h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item rounded-2 text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i> {{ trans('global.logout') }}
                                </a>
                                <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="content-area">
                @if(session('message'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>{{ session('message') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>

            <footer class="mt-auto py-3 text-center text-muted small border-top">
                &copy; {{ date('Y') }} {{ trans('panel.site_title') }}. All rights reserved.
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script src="{{ asset('js/api-service.js') }}"></script>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.querySelector('.sidebar-overlay').classList.toggle('show');
        }

        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-bs-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon(next);
        }

        function updateThemeIcon(theme) {
            const icon = document.querySelector('#theme-icon');
            if(icon) icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars';
        }

        // --- INTERACTIVE BOT LOGIC (LOTTIE) ---
        
        let bubbleTimeout;
        const funnyQuotes = [
            "Working hard or hardly working?",
            "Need a coffee break?",
            "I'm powered by Laravel!",
            "Don't click me, I'm ticklish!",
            "System status: 100% Awesome",
            "Beep Boop! Here to help.",
            "Did you check the dashboard?",
            "Security patrol on duty!",
            "I saw you delete that log...",
            "Coding is magic, right?",
            "Can I get a raise?",
            "Server room is chilly today!"
        ];

        function interactWithBot() {
            const bubble = document.getElementById('botBubble');
            const player = document.getElementById('botPlayer');
            
            if(!bubble || !player) return;

            // 1. Random Quote
            const randomText = funnyQuotes[Math.floor(Math.random() * funnyQuotes.length)];
            bubble.innerText = randomText;
            
            // 2. Show Bubble
            bubble.classList.add('show');
            
            // 3. Replay animation (Excitement)
            player.stop();
            player.play();

            // 4. Hide bubble after 3 seconds
            clearTimeout(bubbleTimeout);
            bubbleTimeout = setTimeout(() => {
                bubble.classList.remove('show');
            }, 3000);
        }
    </script>
    @yield('scripts')
</body>
</html>