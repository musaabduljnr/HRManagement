@extends('install.layout')

@section('content')
<div class="steps-nav">
    <div class="step-dot completed">1</div>
    <div class="step-dot completed">2</div>
    <div class="step-dot completed">3</div>
    <div class="step-dot active">4</div>
    <div class="step-dot">5</div>
</div>

<h3 style="margin-bottom: 1.5rem; font-family: 'Outfit'; font-size: 1.35rem;">Step 4: Create Admin Account</h3>

@if (session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-error" style="padding-bottom: 0.5rem;">
        <ul style="list-style-position: inside;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($adminExists)
    <div class="alert alert-success" style="margin-bottom: 1.5rem;">
        <strong>Admin Account Exists!</strong> An administrator account already exists in the database. You can proceed directly to finalize the installation.
    </div>
@endif

<form action="{{ route('install.step', 4) }}" method="POST">
    {{ csrf_field() }}
    
    @if (!$adminExists)
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name') }}" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name') }}" required>
        </div>

        <div class="form-group">
            <label for="email">Admin Email Address</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>
    @endif

    <button type="submit" class="btn">
        {{ $adminExists ? 'Finalize Installation' : 'Create Admin & Complete Installation' }}
    </button>
</form>
@endsection
