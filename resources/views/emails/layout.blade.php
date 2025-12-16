{{-- filepath: k:\K\KKP\Laravel-Support-Ticketing\resources\views\emails\layout.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $subject ?? 'Krealogi Support' }}</title>
    <style>
        /* Email Client Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            margin: 0; padding: 0; width: 100%; background-color: #f8fafc; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6; color: #374151;
        }
        
        table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { border: 0; line-height: 100%; outline: none; text-decoration: none; max-width: 100%; height: auto; }
        
        /* Container */
        .email-container { 
            max-width: 600px; margin: 0 auto; background: #ffffff; 
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1); 
            border-radius: 16px; overflow: hidden;
        }
        
        /* Header */
        .email-header { 
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            padding: 40px 30px; text-align: center; position: relative;
        }
        
        .email-header::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="3" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="60" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
        }
        
        .logo-container { 
            position: relative; z-index: 2; margin-bottom: 20px; 
        }
        
        .logo { 
            max-width: 200px; height: auto; 
            filter: brightness(0) invert(1); /* Make logo white */
        }
        
        .header-title { 
            color: #ffffff; font-size: 24px; font-weight: 700; 
            margin: 0; position: relative; z-index: 2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Body Content */
        .email-body { 
            padding: 40px 30px; background: #ffffff; 
        }
        
        .email-content h1 { 
            color: #1f2937; font-size: 28px; font-weight: 700; 
            margin: 0 0 20px 0; line-height: 1.3;
        }
        
        .email-content h2 { 
            color: #374151; font-size: 20px; font-weight: 600; 
            margin: 30px 0 15px 0;
        }
        
        .email-content p { 
            margin: 0 0 16px 0; font-size: 16px; line-height: 1.6; 
        }
        
        .email-content .lead { 
            font-size: 18px; color: #6b7280; margin-bottom: 25px; 
        }
        
        /* Ticket Info Card */
        .ticket-card { 
            background: #f8fafc; border: 1px solid #e5e7eb; 
            border-radius: 12px; padding: 20px; margin: 25px 0;
            border-left: 4px solid #3b82f6;
        }
        
        .ticket-title { 
            font-weight: 700; color: #1f2937; font-size: 18px; 
            margin-bottom: 10px; 
        }
        
        .ticket-meta { 
            color: #6b7280; font-size: 14px; margin-bottom: 15px; 
        }
        
        .ticket-content { 
            background: #ffffff; padding: 15px; border-radius: 8px; 
            border: 1px solid #e5e7eb; font-size: 15px;
            max-height: 200px; overflow-y: auto;
        }
        
        /* Action Button */
        .btn-container { 
            text-align: center; margin: 30px 0; 
        }
        
        .btn { 
            display: inline-block; padding: 16px 32px; 
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: #ffffff !important; text-decoration: none; 
            border-radius: 12px; font-weight: 700; font-size: 16px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }
        
        /* Status Badges */
        .status-badge { 
            display: inline-block; padding: 6px 12px; border-radius: 20px; 
            font-size: 12px; font-weight: 600; text-transform: uppercase;
        }
        
        .status-open { background: #ecfccb; color: #365314; }
        .status-progress { background: #fef3c7; color: #92400e; }
        .status-closed { background: #fee2e2; color: #991b1b; }
        
        /* Priority Badges */
        .priority-badge { 
            display: inline-block; padding: 4px 10px; border-radius: 16px; 
            font-size: 11px; font-weight: 600;
        }
        
        .priority-low { background: #d1fae5; color: #047857; }
        .priority-medium { background: #ddd6fe; color: #5b21b6; }
        .priority-high { background: #fed7aa; color: #c2410c; }
        .priority-critical { background: #fecaca; color: #dc2626; }
        
        /* Footer */
        .email-footer { 
            background: #f9fafb; padding: 30px; text-align: center; 
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-text { 
            color: #6b7280; font-size: 14px; margin: 0 0 15px 0; 
        }
        
        .footer-links { margin: 20px 0; }
        .footer-links a { 
            color: #3b82f6; text-decoration: none; margin: 0 15px; 
            font-size: 14px; font-weight: 500;
        }
        
        .social-links { margin: 20px 0; }
        .social-links a { 
            display: inline-block; margin: 0 8px; 
            width: 36px; height: 36px; border-radius: 50%;
            background: #e5e7eb; line-height: 36px; text-align: center;
            color: #6b7280; text-decoration: none;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container { margin: 10px; border-radius: 12px; }
            .email-header, .email-body, .email-footer { padding: 25px 20px; }
            .header-title { font-size: 20px; }
            .email-content h1 { font-size: 24px; }
            .btn { padding: 14px 24px; font-size: 14px; }
        }
        
        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body { background-color: #111827; }
            .email-container { background: #1f2937; }
            .email-body { background: #1f2937; }
            .email-content h1 { color: #f9fafb; }
            .email-content p { color: #d1d5db; }
            .ticket-card { background: #374151; border-color: #4b5563; }
            .ticket-title { color: #f9fafb; }
            .email-footer { background: #374151; border-color: #4b5563; }
        }
    </style>
</head>
<body>
    <div style="padding: 20px 0;">
        <table role="presentation" style="width: 100%; border: none;">
            <tr>
                <td>
                    <div class="email-container">
                        <!-- Header -->
                        <div class="email-header">
                            <div class="logo-container">
                                <img src="{{ asset('images/icon-krealogi.png') }}" alt="Krealogi" class="logo">
                            </div>
                            <h1 class="header-title">{{ $title ?? 'Support Ticketing System' }}</h1>
                        </div>
                        
                        <!-- Body -->
                        <div class="email-body">
                            <div class="email-content">
                                @yield('content')
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="email-footer">
                            <p class="footer-text">
                                <strong>Krealogi Inovasi Digital</strong><br>
                                Your trusted partner in digital innovation and support
                            </p>
                            
                            <div class="footer-links">
                                <a href="{{ config('app.url') }}">Support Portal</a>
                                <a href="mailto:support@krealogi.com">Contact Us</a>
                                <a href="#">Privacy Policy</a>
                            </div>
                            
                            <p class="footer-text">
                                © {{ date('Y') }} Krealogi Inovasi Digital. All rights reserved.
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>