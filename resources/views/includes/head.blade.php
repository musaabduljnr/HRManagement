<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'HRM') }} — HR Management</title>
<meta name="description" content="Human Resource Management System — manage employees, attendance, payroll, and more.">

{{-- Preconnect for font performance --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

{{-- Bootstrap 3.3.7 CSS --}}
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

{{-- Font Awesome 4.7 --}}
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

{{-- App CSS (Bootstrap overrides + custom) --}}
<link rel="stylesheet" type="text/css" href="/css/app.css">

{{-- Modern HRM UI v2 — Premium SaaS Design --}}
<link rel="stylesheet" type="text/css" href="/css/hrm-modern.css?v=2.0.3">