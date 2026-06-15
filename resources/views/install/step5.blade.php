@extends('install.layout')

@section('content')
<div class="steps-nav">
    <div class="step-dot completed">1</div>
    <div class="step-dot completed">2</div>
    <div class="step-dot completed">3</div>
    <div class="step-dot completed">4</div>
    <div class="step-dot completed">5</div>
</div>

<div style="text-align: center; margin-bottom: 2.5rem;">
    <div style="width: 72px; height: 72px; background: rgba(16, 185, 129, 0.1); border: 2px solid var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="#34d399" style="width: 36px; height: 36px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
    </div>
    
    <h3 style="font-family: 'Outfit'; font-size: 1.6rem; margin-bottom: 0.75rem;">Installation Successful!</h3>
    <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.5;">
        HRMan has been fully configured and is ready for use on Railway. The installation wizard is now locked, and the app is ready for normal traffic.
    </p>
</div>

<form action="{{ route('install.step', 5) }}" method="POST">
    {{ csrf_field() }}
    <button type="submit" class="btn">
        Go to Login / Dashboard
    </button>
</form>
@endsection
