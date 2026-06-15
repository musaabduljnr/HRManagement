@extends('install.layout')

@section('content')
<div class="steps-nav">
    <div class="step-dot completed">1</div>
    <div class="step-dot completed">2</div>
    <div class="step-dot active">3</div>
    <div class="step-dot">4</div>
    <div class="step-dot">5</div>
</div>

<h3 style="margin-bottom: 1.5rem; font-family: 'Outfit'; font-size: 1.35rem;">Step 3: Database Migrations</h3>

@if (session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div style="margin-bottom: 2rem; font-size: 0.95rem; line-height: 1.6; color: var(--text-muted);">
    <p style="margin-bottom: 1rem;">We are ready to build the database schema and populate essential settings templates into your MySQL database.</p>
    <p>This process runs the core Laravel migrations and seeds demographic table information (departments, rules, etc.). It will **not** drop any existing data or tables.</p>
</div>

<form action="{{ route('install.step', 3) }}" method="POST" id="migrationForm">
    {{ csrf_field() }}
    <button type="submit" class="btn" id="runBtn">
        Run Migrations & Seeds
    </button>
</form>

<script>
    document.getElementById('migrationForm').addEventListener('submit', function() {
        var btn = document.getElementById('runBtn');
        btn.disabled = true;
        btn.innerText = 'Running migrations, please wait...';
    });
</script>
@endsection
