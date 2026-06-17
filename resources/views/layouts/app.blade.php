<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HRM') }} — Sign In</title>

    <!-- Font preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Bootstrap 3 base -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- App CSS -->
    <link href="/css/app.css" rel="stylesheet">
    <!-- Modern HRM UI v2 — Premium SaaS Design -->
    <link href="/css/hrm-modern.css?v=2.0.3" rel="stylesheet">

    <style>
        /* Login page specific */
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #312e81 100%) !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 440px;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .auth-brand-icon {
            width: 56px;
            height: 56px;
            background: #4f46e5;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            box-shadow: 0 8px 25px rgba(79,70,229,0.4);
        }

        .auth-brand-icon svg {
            width: 32px;
            height: 32px;
            fill: white;
        }

        .auth-brand-name {
            font-size: 22px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
            margin: 0;
        }

        .auth-brand-sub {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 4px;
        }

        .auth-card {
            background: white;
            border-radius: 16px;
            padding: 36px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
        }

        .auth-card-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .auth-card-sub {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 28px;
        }

        .auth-card .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            text-align: left !important;
        }

        .auth-card .form-control {
            height: 44px !important;
            font-size: 14px !important;
            border-radius: 8px !important;
            border: 1.5px solid #e2e8f0 !important;
        }

        .auth-card .form-control:focus {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.12) !important;
        }

        .auth-btn {
            width: 100%;
            height: 46px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .auth-btn:hover {
            background: #3730a3;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(79,70,229,0.35);
        }

        .auth-card .checkbox label {
            font-size: 13px;
            color: #64748b;
            font-weight: 400;
            text-align: left !important;
        }

        .auth-forgot {
            color: #4f46e5;
            font-size: 13px;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-forgot:hover {
            color: #3730a3;
            text-decoration: underline;
        }

        .help-block {
            font-size: 12px;
            color: #ef4444;
            margin-top: 4px;
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <!-- Brand -->
        <div class="auth-brand">
            <div class="auth-brand-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
                </svg>
            </div>
            <h1 class="auth-brand-name">{{ config('app.name', 'HRM') }}</h1>
            <p class="auth-brand-sub">Human Resource Management System</p>
        </div>

        <!-- Auth Card -->
        <div class="auth-card">
            @yield('content')
        </div>

        <div class="auth-footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'HRM') }}. All rights reserved.
        </div>
    </div>

    <!-- Scripts -->
    <script src="//code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>
