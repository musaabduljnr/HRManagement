<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('app.name', 'HRM') }} — HR Management</title>

<!-- Bootstrap 3.3.7 CSS (base grid + components) -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Font Awesome 4 -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- App CSS (Bootstrap overrides + custom) -->
<link rel="stylesheet" type="text/css" href="/css/app.css">

<!-- Modern HRM UI (overrides Bootstrap 3 for SaaS look) -->
<link rel="stylesheet" type="text/css" href="/css/hrm-modern.css">

<style>
  /* Inline CSRF token for JS */
  meta[name="csrf-token"] { display: none; }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">