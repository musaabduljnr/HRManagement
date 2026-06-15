@extends('install.layout')

@section('content')
<div class="steps-nav">
    <div class="step-dot active">1</div>
    <div class="step-dot">2</div>
    <div class="step-dot">3</div>
    <div class="step-dot">4</div>
    <div class="step-dot">5</div>
</div>

<h3 style="margin-bottom: 1.5rem; font-family: 'Outfit'; font-size: 1.35rem;">Step 1: System Requirements</h3>

@if (session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div style="margin-bottom: 2rem;">
    <div class="requirement-item">
        <span>PHP >= 7.0.0 (Current: {{ PHP_VERSION }})</span>
        <span class="status-badge {{ $requirements['php_version'] ? 'success' : 'error' }}">
            {{ $requirements['php_version'] ? 'Passed' : 'Failed' }}
        </span>
    </div>
    <div class="requirement-item">
        <span>OpenSSL PHP Extension</span>
        <span class="status-badge {{ $requirements['openssl'] ? 'success' : 'error' }}">
            {{ $requirements['openssl'] ? 'Enabled' : 'Missing' }}
        </span>
    </div>
    <div class="requirement-item">
        <span>PDO PHP Extension</span>
        <span class="status-badge {{ $requirements['pdo'] ? 'success' : 'error' }}">
            {{ $requirements['pdo'] ? 'Enabled' : 'Missing' }}
        </span>
    </div>
    <div class="requirement-item">
        <span>Mbstring PHP Extension</span>
        <span class="status-badge {{ $requirements['mbstring'] ? 'success' : 'error' }}">
            {{ $requirements['mbstring'] ? 'Enabled' : 'Missing' }}
        </span>
    </div>
    <div class="requirement-item">
        <span>Tokenizer PHP Extension</span>
        <span class="status-badge {{ $requirements['tokenizer'] ? 'success' : 'error' }}">
            {{ $requirements['tokenizer'] ? 'Enabled' : 'Missing' }}
        </span>
    </div>
    <div class="requirement-item">
        <span>XML PHP Extension</span>
        <span class="status-badge {{ $requirements['xml'] ? 'success' : 'error' }}">
            {{ $requirements['xml'] ? 'Enabled' : 'Missing' }}
        </span>
    </div>
    <div class="requirement-item">
        <span>GD PHP Extension</span>
        <span class="status-badge {{ $requirements['gd'] ? 'success' : 'error' }}">
            {{ $requirements['gd'] ? 'Enabled' : 'Missing' }}
        </span>
    </div>
    <div class="requirement-item">
        <span>Zip PHP Extension</span>
        <span class="status-badge {{ $requirements['zip'] ? 'success' : 'error' }}">
            {{ $requirements['zip'] ? 'Enabled' : 'Missing' }}
        </span>
    </div>
    <div class="requirement-item">
        <span>Storage Directory Writable</span>
        <span class="status-badge {{ $requirements['storage_writable'] ? 'success' : 'error' }}">
            {{ $requirements['storage_writable'] ? 'Writable' : 'Not Writable' }}
        </span>
    </div>
    <div class="requirement-item">
        <span>Bootstrap Cache Directory Writable</span>
        <span class="status-badge {{ $requirements['cache_writable'] ? 'success' : 'error' }}">
            {{ $requirements['cache_writable'] ? 'Writable' : 'Not Writable' }}
        </span>
    </div>
</div>

<form action="{{ route('install.step', 1) }}" method="POST">
    {{ csrf_field() }}
    <button type="submit" class="btn" {{ in_array(false, $requirements, true) ? 'disabled' : '' }}>
        Next: Configure Database
    </button>
</form>
@endsection
