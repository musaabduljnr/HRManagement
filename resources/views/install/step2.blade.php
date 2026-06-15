@extends('install.layout')

@section('content')
<div class="steps-nav">
    <div class="step-dot completed">1</div>
    <div class="step-dot active">2</div>
    <div class="step-dot">3</div>
    <div class="step-dot">4</div>
    <div class="step-dot">5</div>
</div>

<h3 style="margin-bottom: 1.5rem; font-family: 'Outfit'; font-size: 1.35rem;">Step 2: Database Connection</h3>

@if (session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

@if ($dbConnected)
    <div class="alert alert-success">
        <strong>Connected Successfully!</strong> The application can communicate with the MySQL database service on Railway.
    </div>
@else
    <div class="alert alert-error" style="margin-bottom: 1.5rem;">
        <strong>Connection Failed!</strong><br>
        {{ $dbError }}
        <p style="margin-top: 0.5rem; font-size: 0.85rem; color: #fca5a5;">
            Please ensure you have linked the MySQL database variables (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) in the Railway dashboard.
        </p>
    </div>
@endif

<div class="db-info">
    <div style="font-weight: 600; font-size: 0.95rem; margin-bottom: 0.75rem; color: var(--text-main);">Current Database Configuration:</div>
    <div class="db-info-row">
        <span>Connection Driver</span>
        <span>{{ $dbConfig['connection'] }}</span>
    </div>
    <div class="db-info-row">
        <span>Host Address</span>
        <span>{{ $dbConfig['host'] }}</span>
    </div>
    <div class="db-info-row">
        <span>Port</span>
        <span>{{ $dbConfig['port'] }}</span>
    </div>
    <div class="db-info-row">
        <span>Database Name</span>
        <span>{{ $dbConfig['database'] }}</span>
    </div>
    <div class="db-info-row">
        <span>Username</span>
        <span>{{ $dbConfig['username'] }}</span>
    </div>
</div>

<form action="{{ route('install.step', 2) }}" method="POST">
    {{ csrf_field() }}
    
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('install.index') }}" class="btn" style="background: #1f2937; color: var(--text-main); flex: 1; border: 1px solid var(--border-color);">
            Test Again
        </a>
        <button type="submit" class="btn" style="flex: 2;" {{ !$dbConnected ? 'disabled' : '' }}>
            Next: Run Migrations
        </button>
    </div>
</form>
@endsection
