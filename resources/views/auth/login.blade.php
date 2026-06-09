@extends('layouts.app')

@section('content')
<h2 class="auth-card-title">Welcome back</h2>
<p class="auth-card-sub">Sign in to your HR Management account</p>

<form role="form" method="POST" action="{{ url('/login') }}">
    {{ csrf_field() }}

    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
        <label for="email">Email Address</label>
        <input id="email" type="email" class="form-control" name="email"
               value="{{ old('email') }}" required autofocus
               placeholder="you@company.com">
        @if ($errors->has('email'))
            <span class="help-block">{{ $errors->first('email') }}</span>
        @endif
    </div>

    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}" style="margin-bottom: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
            <label for="password" style="margin: 0;">Password</label>
            <a class="auth-forgot" href="{{ url('/password/reset') }}">Forgot password?</a>
        </div>
        <input id="password" type="password" class="form-control" name="password"
               required placeholder="••••••••">
        @if ($errors->has('password'))
            <span class="help-block">{{ $errors->first('password') }}</span>
        @endif
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
        <div class="checkbox" style="margin: 0;">
            <label style="display: flex; align-items: center; gap: 8px; font-size:13px;">
                <input type="checkbox" name="remember" style="width: 15px; height: 15px; flex-shrink:0;">
                Keep me signed in
            </label>
        </div>
    </div>

    <button type="submit" class="auth-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
            <polyline points="10 17 15 12 10 7"/>
            <line x1="15" y1="12" x2="3" y2="12"/>
        </svg>
        Sign In
    </button>
</form>
@endsection
