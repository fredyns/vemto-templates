<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'CSC') }}</title>

<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

<!-- Styles -->
<% if(this.project.usesMixAsCompiler()) { %>
<link rel="stylesheet" href="{{ mix('css/app.css') }}">
<% } %>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

<!-- Icons -->
<link href="https://unpkg.com/ionicons@4.5.10-0/dist/css/ionicons.min.css" rel="stylesheet">

<!-- Scripts -->
<% if(this.project.usesMixAsCompiler()) { %>
<script src="{{ mix('js/app.js') }}" defer></script>
<% } %>
<% if(this.project.usesViteAsCompiler()) { %>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<% } %>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>